import CanvasJS from '@canvasjs/charts';

let captcha_graph = document.querySelector('#captcha_graph');
let captcha_valides = captcha_graph.dataset.captcha_valides;
let captcha_non_valides = captcha_graph.dataset.captcha_non_valides;
let captcha_expires = captcha_graph.dataset.captcha_expires;

let chart = new CanvasJS.Chart("captcha_graph", {
    culture: "fr",
    animationEnabled: true,
    legend: {
        cursor: "pointer",
        itemclick: toggleDataSeries
    },
    toolTip: {
        shared: true
    },
    axisX:{
        valueFormatString: "MMM",
    },
    data: [
        {
            type: "area",
            name: "Validés",
            showInLegend: "true",
            xValueType: "dateTime",
            color: "green",
            dataPoints: JSON.parse(captcha_valides)
        },
        {
            type: "area",
            name: "Non validés",
            showInLegend: "true",
            xValueType: "dateTime",
            color: "red",
            dataPoints: JSON.parse(captcha_non_valides)
        },
        {
            type: "area",
            name: "Expirés",
            showInLegend: "true",
            xValueType: "dateTime",
            color: "orange",
            dataPoints: JSON.parse(captcha_expires)
        }
    ]
});

chart.render();

function toggleDataSeries(e) {
    if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
        e.dataSeries.visible = false;
    } else {
        e.dataSeries.visible = true;
    }
    chart.render();
}
