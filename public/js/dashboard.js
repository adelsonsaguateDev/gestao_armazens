"use strict";


$(document).ready(function () {

  fetch('/dashboard/requisicoes_grafico_barra')
  .then(response => response.json())
  .then(data => {
    // Preparar os dados para o gráfico
    const meses = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
    const pendentes = Array(12).fill(0); // Estado 1
    const aprovadas = Array(12).fill(0); // Estado 2
    const reprovadas = Array(12).fill(0); // Estado 3

    // Preencher os arrays com os dados recebidos
    data.forEach(item => {
      pendentes[item.mes - 1] = item.total_pendentes; // Mês começa em 0 no array
      aprovadas[item.mes - 1] = item.total_aprovadas;
      reprovadas[item.mes - 1] = item.total_reprovadas; 
    });

    // Configurar o gráfico
    var echartElemBar = document.getElementById('echartBar');
    if (echartElemBar) {
      var echartBar = echarts.init(echartElemBar);
      echartBar.setOption({
        legend: {
          borderRadius: 0,
          orient: 'horizontal',
          x: 'right',
          data: ['Pendentes','Aprovadas','Reprovadas']
        },
        grid: {
          left: '8px',
          right: '8px',
          bottom: '0',
          containLabel: true
        },
        tooltip: {
          show: true,
          backgroundColor: 'rgba(0, 0, 0, .8)'
        },
        xAxis: [{
          type: 'category',
          data: meses,
          axisTick: {
            alignWithLabel: true
          },
          splitLine: {
            show: false
          },
          axisLine: {
            show: true
          }
        }],
        yAxis: [{
          type: 'value',
          axisLabel: {
            formatter: '{value}'
          },
          min: 0,
          interval: 10,
          axisLine: {
            show: false
          },
          splitLine: {
            show: true,
            interval: 'auto'
          }
        }],
        series: [
            
            {
            name: 'Pendentes',
            data: pendentes,
            type: 'bar',
            color: '#ffc107',
            smooth: true,
            itemStyle: {
                emphasis: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowOffsetY: -2,
                shadowColor: 'rgba(0, 0, 0, 0.3)'
                }
            }
            }, {
            name: 'Aprovadas',
            data: aprovadas,
            type: 'bar',
            color: '#20c997',
            smooth: true,
            itemStyle: {
                emphasis: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowOffsetY: -2,
                shadowColor: 'rgba(0, 0, 0, 0.3)'
                }
            }
            },{
                name: 'Reprovadas',
                data: reprovadas,
                type: 'bar',
                color: '#f44336',
                smooth: true,
                itemStyle: {
                    emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowOffsetY: -2,
                    shadowColor: 'rgba(0, 0, 0, 0.3)'
                    }
                }
            }
        ]
      });

      $(window).on('resize', function () {
        setTimeout(function () {
          echartBar.resize();
        }, 500);
      });
    }
  });



  var echartElemPie = document.getElementById('echartPie');

if (echartElemPie) {
    var echartPie = echarts.init(echartElemPie);

    // Fazer a requisição com fetch
    fetch('/dashboard/requisicoes_grafico_pizza')
      .then(response => response.json())  // Converte a resposta para JSON
      .then(data => {

        // Verificar se o data tem os campos corretos
        var chartData = data.map(function (item) {
          return {
            value: item.total,  // Total de requisições
            name: item.estado_requisicao_nome  // Nome (PENDENTES, APROVADAS, REPROVADAS)
          };
        });

        // Configurar e renderizar o gráfico
        echartPie.setOption({
          color: ['#ffc107', '#20c997', '#f44336'],
          tooltip: {
            show: true,
            backgroundColor: 'rgba(0, 0, 0, .8)'
          },
          series: [{
            name: 'Total de requisições',
            type: 'pie',
            radius: '60%',
            center: ['50%', '50%'],
            data: chartData,  // Preenche com os dados da API
            itemStyle: {
              emphasis: {
                shadowBlur: 10,
                shadowOffsetX: 0,
                shadowColor: 'rgba(0, 0, 0, 0.5)'
              }
            },
            label: {
              formatter: '{b}: {c}',  // Exibe o nome e o valor
              position: 'outside',
            }
          }]
        });

        // Ajustar o gráfico quando a janela for redimensionada
        $(window).on('resize', function () {
          setTimeout(function () {
            echartPie.resize();
          }, 500);
        });
      })
      .catch(error => {
        console.error('Erro ao obter dados:', error);
      });
  }



});