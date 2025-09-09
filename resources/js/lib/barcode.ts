import JsBarcode from 'jsbarcode';

export interface BarcodeOptions {
  format?: 'CODE128' | 'CODE39' | 'EAN13' | 'EAN8' | 'UPC' | 'ITF14' | 'MSI' | 'pharmacode' | 'codabar';
  width?: number;
  height?: number;
  displayValue?: boolean;
  fontSize?: number;
  margin?: number;
  background?: string;
  lineColor?: string;
  textAlign?: 'left' | 'center' | 'right';
  textPosition?: 'bottom' | 'top';
  textMargin?: number;
  font?: string;
  fontOptions?: 'bold' | 'normal';
  valid?: (valid: boolean) => void;
}

export interface BarcodeData {
  kode_barang: string;
  nama_barang: string;
  kategori: string;
  stok: number;
  satuan?: string;
  lokasi?: string;
}

/**
 * Generate barcode as SVG string
 */
export function generateBarcodeSVG(
  text: string, 
  options: BarcodeOptions = {}
): string {
  const defaultOptions: BarcodeOptions = {
    format: 'CODE128',
    width: 2,
    height: 100,
    displayValue: true,
    fontSize: 14,
    margin: 10,
    background: '#ffffff',
    lineColor: '#000000',
    textAlign: 'center',
    textPosition: 'bottom',
    textMargin: 2,
    font: 'monospace',
    fontOptions: 'normal',
    ...options
  };

  try {
    const canvas = document.createElement('canvas');
    JsBarcode(canvas, text, defaultOptions);
    
    // Convert canvas to SVG
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', canvas.width.toString());
    svg.setAttribute('height', canvas.height.toString());
    
    const img = document.createElement('img');
    img.src = canvas.toDataURL('image/png');
    
    return canvas.toDataURL('image/png');
  } catch (error) {
    console.error('Error generating barcode:', error);
    throw new Error('Failed to generate barcode');
  }
}

/**
 * Generate barcode as data URL (for direct use in img src)
 */
export function generateBarcodeDataURL(
  text: string, 
  options: BarcodeOptions = {}
): string {
  const defaultOptions: BarcodeOptions = {
    format: 'CODE128',
    width: 2,
    height: 100,
    displayValue: true,
    fontSize: 14,
    margin: 10,
    background: '#ffffff',
    lineColor: '#000000',
    textAlign: 'center',
    textPosition: 'bottom',
    textMargin: 2,
    font: 'monospace',
    fontOptions: 'normal',
    ...options
  };

  try {
    const canvas = document.createElement('canvas');
    JsBarcode(canvas, text, defaultOptions);
    return canvas.toDataURL('image/png');
  } catch (error) {
    console.error('Error generating barcode:', error);
    throw new Error('Failed to generate barcode');
  }
}

/**
 * Generate barcode and download as PNG
 */
export function downloadBarcode(
  text: string, 
  filename: string = 'barcode.png',
  options: BarcodeOptions = {}
): void {
  try {
    const dataURL = generateBarcodeDataURL(text, options);
    const link = document.createElement('a');
    link.download = filename;
    link.href = dataURL;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  } catch (error) {
    console.error('Error downloading barcode:', error);
    throw new Error('Failed to download barcode');
  }
}

/**
 * Generate barcode for barang item
 */
export function generateBarangBarcode(
  barang: BarcodeData,
  options: BarcodeOptions = {}
): string {
  const barcodeOptions: BarcodeOptions = {
    format: 'CODE128',
    width: 2,
    height: 80,
    displayValue: true,
    fontSize: 12,
    margin: 8,
    background: '#ffffff',
    lineColor: '#000000',
    textAlign: 'center',
    textPosition: 'bottom',
    textMargin: 2,
    font: 'monospace',
    fontOptions: 'normal',
    ...options
  };

  return generateBarcodeDataURL(barang.kode_barang, barcodeOptions);
}

/**
 * Generate multiple barcodes for batch printing
 */
export function generateBatchBarcodes(
  barangList: BarcodeData[],
  options: BarcodeOptions = {}
): string[] {
  return barangList.map(barang => generateBarangBarcode(barang, options));
}

/**
 * Validate barcode format
 */
export function validateBarcodeFormat(text: string, format: string = 'CODE128'): boolean {
  try {
    const canvas = document.createElement('canvas');
    JsBarcode(canvas, text, { format: format as any });
    return true;
  } catch (error) {
    return false;
  }
}

/**
 * Get supported barcode formats
 */
export function getSupportedFormats(): string[] {
  return [
    'CODE128',
    'CODE39', 
    'EAN13',
    'EAN8',
    'UPC',
    'ITF14',
    'MSI',
    'pharmacode',
    'codabar'
  ];
}

/**
 * Format barcode text for display
 */
export function formatBarcodeText(text: string, maxLength: number = 20): string {
  if (text.length <= maxLength) {
    return text;
  }
  return text.substring(0, maxLength - 3) + '...';
}

/**
 * Generate barcode with custom styling for different categories
 */
export function generateStyledBarcode(
  text: string,
  kategori: 'peminjaman' | 'permintaan',
  options: BarcodeOptions = {}
): string {
  const categoryColors = {
    peminjaman: {
      lineColor: '#1e40af', // blue-800
      background: '#dbeafe' // blue-100
    },
    permintaan: {
      lineColor: '#059669', // emerald-600
      background: '#d1fae5' // emerald-100
    }
  };

  const categoryStyle = categoryColors[kategori];
  
  const styledOptions: BarcodeOptions = {
    format: 'CODE128',
    width: 2,
    height: 80,
    displayValue: true,
    fontSize: 12,
    margin: 8,
    background: categoryStyle.background,
    lineColor: categoryStyle.lineColor,
    textAlign: 'center',
    textPosition: 'bottom',
    textMargin: 2,
    font: 'monospace',
    fontOptions: 'normal',
    ...options
  };

  return generateBarcodeDataURL(text, styledOptions);
}
