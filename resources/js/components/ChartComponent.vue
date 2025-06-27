<template>
  <div class="chart-container group">
    <canvas :id="chartId" ref="chartCanvas"></canvas>
    <div class="chart-overlay absolute inset-0 bg-gradient-to-br from-transparent to-white/5 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-lg pointer-events-none"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch } from 'vue';

interface ChartData {
  labels: string[];
  datasets: {
    label?: string;
    data: number[];
    backgroundColor?: string | string[];
    borderColor?: string | string[];
    hoverBackgroundColor?: string | string[];
    hoverBorderColor?: string | string[];
    borderWidth?: number;
    fill?: boolean;
    tension?: number;
    pointRadius?: number;
    pointBackgroundColor?: string | string[];
    pointBorderColor?: string | string[];
    pointHoverRadius?: number;
    pointHoverBackgroundColor?: string | string[];
    pointHoverBorderColor?: string | string[];
  }[];
}

interface ChartOptions {
  type: 'line' | 'bar' | 'doughnut' | 'pie';
  data: ChartData;
  options?: any;
}

interface Props {
  chartId: string;
  chartData: ChartOptions;
}

const props = defineProps<Props>();
const chartCanvas = ref<HTMLCanvasElement>();
let chartInstance: any = null;

// Declare Chart.js globally
declare global {
  interface Window {
    Chart: any;
  }
}

const createChart = () => {
  if (!chartCanvas.value || !window.Chart) return;

  // Destroy existing chart if it exists
  if (chartInstance) {
    chartInstance.destroy();
  }

  const ctx = chartCanvas.value.getContext('2d');
  if (!ctx) return;

  // Set default font family and color for Chart.js v3+
  window.Chart.defaults.font.family = 'Nunito, -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif';
  window.Chart.defaults.color = '#858796';

  // Create chart with default options
  const defaultOptions = {
    maintainAspectRatio: false,
    responsive: true,
    layout: {
      padding: {
        left: 10,
        right: 25,
        top: 25,
        bottom: 0
      }
    },
    scales: props.chartData.type === 'line' || props.chartData.type === 'bar' ? {
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          maxTicksLimit: 7,
          fontColor: '#858796'
        }
      }],
      yAxes: [{
        ticks: {
          maxTicksLimit: 5,
          padding: 10,
          fontColor: '#858796',
          callback: function(value: any, index: any, values: any) {
            return value.toLocaleString();
          }
        },
        gridLines: {
          color: "rgb(234, 236, 244)",
          zeroLineColor: "rgb(234, 236, 244)",
          drawBorder: false,
          borderDash: [2],
          zeroLineBorderDash: [2]
        }
      }]
    } : undefined,
    legend: {
      display: false
    },
    tooltips: {
      backgroundColor: "rgb(255,255,255)",
      bodyFontColor: "#858796",
      titleMarginBottom: 10,
      titleFontColor: '#6e707e',
      titleFontSize: 14,
      borderColor: '#dddfeb',
      borderWidth: 1,
      xPadding: 15,
      yPadding: 15,
      displayColors: false,
      intersect: false,
      mode: 'index',
      caretPadding: 10,
      callbacks: {
        label: function(tooltipItem: any, chart: any) {
          const datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
          return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString();
        }
      }
    },
    animation: {
      duration: 2000,
      easing: 'easeInOutQuart'
    },
    hover: {
      animationDuration: 300
    }
  };

  // Merge default options with custom options
  const finalOptions = {
    ...defaultOptions,
    ...props.chartData.options
  };

  // Special handling for doughnut/pie charts
  if (props.chartData.type === 'doughnut' || props.chartData.type === 'pie') {
    finalOptions.cutoutPercentage = 80;
    delete finalOptions.scales;
  }

  chartInstance = new window.Chart(ctx, {
    type: props.chartData.type,
    data: props.chartData.data,
    options: finalOptions
  });
};

onMounted(() => {
  // Wait for Chart.js to be loaded
  const checkChart = () => {
    if (window.Chart) {
      createChart();
    } else {
      setTimeout(checkChart, 100);
    }
  };
  checkChart();
});

onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});

// Watch for changes in chart data
watch(() => props.chartData, () => {
  createChart();
}, { deep: true });
</script>

<style scoped>
.chart-container {
  position: relative;
  height: 300px;
  width: 100%;
  border-radius: 0.5rem;
  overflow: hidden;
  transition: all 0.3s ease;
}

.chart-container:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.chart-overlay {
  background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.05) 100%);
}

@media (min-width: 768px) {
  .chart-container {
    height: 400px;
  }
}

/* Chart.js canvas styling */
canvas {
  border-radius: 0.5rem;
}
</style> 