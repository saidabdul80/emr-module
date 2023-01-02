<?php

/**
 * Sample HTML page
 *
 * @package   OpenEMR
 * @link      http://www.open-emr.org
 *
 * @author    Stephen Nielson <stephen@nielson.org>
 * @copyright Copyright (c) 2021 Stephen Nielson <stephen@nielson.org>
 * @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

?>
<html>
<title>Smart Device</title>
<link rel="stylesheet" href="assets/css/bootstrap.css">
<link rel="stylesheet" href="assets/css/datatable.css">
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/jquery.js"></script>
<script src="assets/js/propper.js"></script>
<script src="assets/js/axios.js"></script>
<script src="assets/js/jquery-datatable.js"></script>
<script src="assets/js/chart.js"></script>
<script src="assets/js/vue.js"></script>
<script src="main.js"></script>

<body class="row w-100 mx-auto">
    <div class="col-xs-12 col-md-2 col-lg-1"></div>
    <div class="col-xs-12 col-md-8 col-lg-10 w-100 mx-0 pb-0 bg-white position-relative" id="app">
        <div id="loaderHtml" class="w-100 position-absolute xloader">
            <div class="middle">
                <div class="bar bar1"></div>
                <div class="bar bar2"></div>
                <div class="bar bar3"></div>
                <div class="bar bar4"></div>
                <div class="bar bar5"></div>
                <div class="bar bar6"></div>
                <div class="bar bar7"></div>
                <div class="bar bar8"></div>
            </div>
        </div>
        <div id="loader" class="w-100 position-absolute">
            <div class="spinner-border" role="status" style="width: 60px;height:60px">
            </div>
        </div>
        <div id="appM">
            <p class="p-3 bg-light">Patient Records from devices</p>
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Fitbit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">GoogleFit</a>
                </li>
            </ul>

            <div class="row w-100 pt-3">
                <div class="col-md-6 mb-3">
                    <div class="border rounded" style="height: 350px;min-width:200px">
                        <h5 class="px-3 pt-3">Heart Rate</h5>
                        <hr>
                        <div style="position: relative;">
                            <div><canvas id="HeartRate" style="position: absolute;"></canvas></div>
                        </div>   
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded" style="height: 350px;min-width:200px">
                        <h5 class="px-3 pt-3">Blood Pressure</h5>
                        <hr>
                        <div style="position: relative;">
                        <div><canvas id="BloodPressure" style="position:absolute;"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded" style="height: 350px;min-width:200px">
                        <h5 class="px-3 pt-3">Sleep</h5>
                        <hr>
                        <div style="position: relative;">
                        <div><canvas id="Sleep" style="position:absolute;"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="border rounded" style="height: 350px;min-width:200px">
                        <h5 class="px-3 pt-3">Activity</h5>
                        <hr>
                        <div style="position: relative;">
                        <div><canvas id="Activity" style="position:absolute;"></canvas></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6"></div>
                <div class="col-md-6"></div>
            </div>
        </div>
    </div>
    <script>
        const {
            createApp
        } = Vue
        createApp({
            data() {
                return {

                }
            },
            methods: {
                initChart(data,labels,id,name){
                    const ctx = $('#'+id);
                    console.log(ctx,4);
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: '# of '+name,
                                data: data,
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            },
            mounted() {  
                this.initChart([12, 19, 3, 5, 2, 3],['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],'HeartRate','Heart Rate')                                              
                this.initChart([12, 19, 3, 5, 2, 3],['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],'BloodPressure','Blood Pressure')                              
                this.initChart([12, 19, 3, 5, 2, 3],['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],'Sleep','Sleep')                              
                this.initChart([12, 19, 3, 5, 2, 3],['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],'Activity','Activity')                              
            }

        }).mount('#appM')

        $(document).ready(function(){
                   
                })
        $(document).ready(function() {
            
         /*    const config = {
                type: 'line',
                data: data,
                options: {
                    plugins: {
                        filler: {
                            propagate: false,
                        },
                        title: {
                            display: true,
                            text: (ctx) => 'Fill: ' + ctx.chart.data.datasets[0].fill
                        }
                    },
                    interaction: {
                        intersect: false,
                    }
                },
            };

            const data = {
                labels: generateLabels(),
                datasets: [{
                    label: 'Dataset',
                    data: generateData(),
                    borderColor: Utils.CHART_COLORS.red,
                    backgroundColor: Utils.transparentize(Utils.CHART_COLORS.red),
                    fill: false
                }]
            };
            const inputs = {
                min: -100,
                max: 100,
                count: 8,
                decimals: 2,
                continuity: 1
            }; */

            /* const generateLabels = () => {
                return Utils.months({
                    count: inputs.count
                });
            };

            const generateData = () => (Utils.numbers(inputs));
            */          
            $('#tablexxxx').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'excel',
                    'pdf'
                ],
                pageLength: 6,

            });
            setTimeout(function() {
                
                switchPage()

            }, 1000)
        })
    </script>
</body>

</html>