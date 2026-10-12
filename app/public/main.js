google.charts.load('current', {
    packages: ['corechart'],
    language: 'es-AR'
});

google.charts.setOnLoadCallback(drawChart);


function drawChart() {

    // Recuperamos los datos generados por PHP.
    const chartDataElement = document.getElementById('chart-data');
    const chartElement = document.getElementById('chart_div');

    if (!chartDataElement || !chartElement) {
        return;
    }

    const prices = JSON.parse(chartDataElement.textContent);

    if (prices.length === 0) {
        return;
    }

    const dataArray = [
        ['Fecha', 'Precio', { role: 'style' }]
    ];

    prices.forEach(function (price) {
        dataArray.push([
            price.date,
            price.price,
            '#0ABF00'
        ]);
    });

    const data = google.visualization.arrayToDataTable(dataArray);

    const priceCount = Number(
        chartElement.dataset.priceCount
    );

    const groupWidth = priceCount > 15 ? '70%' : '90%';

    const options = {

        tooltip: {
            trigger: 'focus',

            textStyle: {
                fontName: 'VT323',
                fontSize: 25,
                bold: true,
                color: '#000000ff'
            }
        },

        backgroundColor: 'transparent',

        // Sin animación nativa de Google Charts.
        animation: {
            startup: false
        },

        chartArea: {
            left: 5,
            top: 5,
            right: 5,
            bottom: 5,
            width: '94%',
            height: '94%'
        },

        bar: {
            groupWidth: groupWidth
        },

        hAxis: {
            textPosition: 'none',
            baselineColor: 'transparent',

            gridlines: {
                color: 'transparent'
            },

            minorGridlines: {
                color: 'transparent'
            }
        },

        vAxis: {
            format: '$#,##0',
            textPosition: 'none',

            viewWindow: {
                min: Number(chartElement.dataset.minPrice),
                max: Number(chartElement.dataset.maxPrice)
            },

            textStyle: {
                color: '#0ABF00',
                fontSize: 20
            },

            baselineColor: '#ff0000ff',

            gridlines: {
                color: 'none'
            },

            minorGridlines: {
                color: 'none'
            }
        },

        legend: {
            position: 'none'
        }
    };

    const chart = new google.visualization.ColumnChart(
        chartElement
    );

    // Dibujamos el gráfico una sola vez.
    chart.draw(data, options);

    // Esperamos a que Google genere el SVG.
    setTimeout(function () {
        prepararAnimacion();
    }, 100);


    function prepararAnimacion() {

        const svg = chartElement.querySelector('svg');

        if (!svg) {
            return;
        }

        const rects = svg.querySelectorAll('rect');
        const barras = [];

        rects.forEach(function (rect) {

            const fill = rect.getAttribute('fill');

            if (fill === '#0ABF00') {

                const y = parseFloat(rect.getAttribute('y'));
                const height = parseFloat(
                    rect.getAttribute('height')
                );

                if (
                    !isNaN(y) &&
                    !isNaN(height) &&
                    height > 0
                ) {
                    barras.push({
                        rect: rect,
                        y: y,
                        height: height,
                        bottom: y + height
                    });
                }
            }
        });

        if (barras.length === 0) {
            return;
        }

        // Ocultamos las barras inicialmente.
        barras.forEach(function (barra) {

            barra.rect.setAttribute('height', 0);

            barra.rect.setAttribute(
                'y',
                barra.bottom
            );
        });

        // Primer fotograma: 30 %.
        setTimeout(function () {
            cambiarBarras(barras, 0.30);
        }, 100);

        // Segundo fotograma: 60 %.
        setTimeout(function () {
            cambiarBarras(barras, 0.60);
        }, 1100);

        // Tercer fotograma: 100 %.
        setTimeout(function () {
            cambiarBarras(barras, 1.00);
        }, 2100);
    }


    function cambiarBarras(barras, porcentaje) {

        barras.forEach(function (barra) {

            const nuevaAltura = barra.height * porcentaje;
            const nuevoY = barra.bottom - nuevaAltura;

            // Cambio instantáneo, sin transición CSS.
            barra.rect.setAttribute(
                'height',
                nuevaAltura
            );

            barra.rect.setAttribute(
                'y',
                nuevoY
            );
        });
    }
}