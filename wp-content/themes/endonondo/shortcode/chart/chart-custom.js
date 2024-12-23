const myChart1 = document.getElementById("myChartBar").getContext("2d");
new Chart(myChart1, {
  plugins: [ChartDataLabels],
  type: 'bar',
  data: {
    labels: ["2009", "2010", "2011", "2012"],
    datasets: [{
      label: '29 years',
      backgroundColor: "#D9D9D9",
      data: [10, 10, 2, 17],
    }, {
      label: '29 years',
      backgroundColor: "#A3A3A3",
      data: [20, 16, 14, 20],
    }, {
      label: '29 years',
      backgroundColor: "#6A6A6A",
      data: [23, 25, 23, 18],
    }, {
      label: '29 years',
      backgroundColor: "#151515",
      data: [31, 29, 31, 26],
    },
    ],
  }
});