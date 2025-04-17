const chartData = {
    labels: ['On-Time', 'Late', 'Missed'],
    data: [30, 5, 10],
};

const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
const dates = ['10/11/2021', '10/12/2021', '10/13/2021', '10/14/2021', '10/15/2021', '10/16/2021', '10/17/2021'];
const times = ['8:00 AM', '9:00 AM', '10:00 AM', '11:00 AM', '12:00 PM', '1:00 PM', '2:00 PM'];

const ul = document.querySelector(".legend ul");
const tb = document.querySelector(".record tbody");

new Chart("myChart", {
    type: "doughnut",
    data: {
        labels: chartData.labels,
        datasets: [{
            data: chartData.data,
            backgroundColor: [
                'rgb(119,221,119)',
                'rgb(255, 205, 86)',
                'rgb(243,71,77)'
            ]
        }]
    },
    options: {
        borderWidth: 2,
        borderRadius: 3,
        hoverBorderWidth: 3,
        cutout: '70%',
        plugins: {
            legend: {
                display: false,
            },
            annotation: {
                annotations: {
                    dLabel: {
                        type: 'doughnutLabel',
                        content: ({ chart }) => ['Total',
                            chart.getDatasetMeta(0).total,
                            'last 12 months'
                        ],
                        font: [{ size: 30 }, { size: 40 }, { size: 20 }],
                        color: ['black', 'black', 'grey']
                    }
                }
            }
        }
    }
});

const populateTable = () => {

}

populateTable();

/* const populateLegend = () => {
     chartData.labels.forEach((l, d) => {
         let li = document.createElement("li");
         li.innerHTML = `${l}: <span class="number">${chartData.data[d]}</span>`;
         ul.appendChild(li);
     })
 };*/
const populateLegend = () => {
    const onTimeDays = document.getElementById("on-time-days");
    const lateDays = document.getElementById("late-days");
    const missedDays = document.getElementById("missed-days");

    onTimeDays.innerText = chartData.data[0] + " " + chartData.labels[0];
    lateDays.innerHTML = chartData.data[1] + " " + chartData.labels[1];
    missedDays.innerHTML = chartData.data[2] + " " + chartData.labels[2];
}

populateLegend();