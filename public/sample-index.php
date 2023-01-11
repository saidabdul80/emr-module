<?php
require_once("../../../../globals.php");
require '../vendor/autoload.php';
//require '../../../../../src/Common/Uuid/UuidRegistry.php';
use OpenEMR\Services\PatientService;

$pService = new PatientService;

$res = sqlStatement("SELECT * FROM pghd_auth WHERE id = 1");
$row = sqlFetchArray($res);
$token = $row['token'];
$base_url = $row['base_url'];

$client = new GuzzleHttp\Client([
    'base_url' => $base_url
]);

echo $token;
$response = $client->request('POST', $base_url.'fetch_tokens', [
    'form_params' => [
        'token' => $token,
    ]
]);

$pids = json_decode($response->getBody()->getContents());

$patientId = $_SESSION['pid'] ?? 0;
foreach ($pids->patients as $key => &$p) {
    $r = $pService->getOne($p->emr_pid);
    $p->details = $r->getData()[0];
}
$patientPortal = 0;
$patient = [];
if (isset($_SESSION['pid']) && isset($_SESSION['patient_portal_onsite_two'])) {
    $pid = $_SESSION['pid'];
    $patient = $pService->findByPid($pid);
    $patientPortal = 1;
}

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
<link rel="stylesheet" href="assets/css/picker.css">
<script src="assets/js/jquery.js"></script>
<script src="assets/js/propper.js"></script>
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/picker.js"></script>
<script src="assets/js/axios.js"></script>
<script src="assets/js/jquery-datatable.js"></script>
<script src="assets/js/date-fns.js"></script>
<script src="assets/js/chart.js"></script>
<script src="assets/js/apex-chart.js"></script>
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
            <div class="row w-100 p-3 bg-light mx-auto">
                <div class="col-md-8">
                    <p class="">Patient Records from <b> {{currentSource}} </b></p>
                </div>
                <div class="col-md-4" v-if="!patientPortal">
                    <select @change="selectPatient()" v-model="patient_id" class="form-control selectpicker">
                        <option value=""></option>
                        <option v-for="patient in patients" :value="patient.emr_pid">{{patient.details.fname}} {{patient.details.lname}}</option>
                    </select>
                </div>
            </div>
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a @click="currentSource='fitbit'" :class="currentSource=='fitbit'?'nav-link active':'nav-link'" aria-current="page" href="#">Fitbit</a>
                </li>
                <li class="nav-item">
                    <a @click="currentSource='googlefit'" :class="currentSource=='googlefit'?'nav-link active':'nav-link'" href="#">GoogleFit</a>
                </li>
            </ul>
            <div class="row w-100 mx-auto py-3 bg-light">
                <div class="col-md-4">
                    <input type="date" class="form-control" v-model="from">
                </div>
                <div class="col-md-4">
                    <input type="date" class="form-control" v-model="to">
                </div>
                <div class="col-sm-6 col-md-1">
                    <button class="btn btn-light text-dark border">Update</button>
                </div>
                <div class="col-sm-6 col-md-3">
                    <button class="btn btn-light text-dark border" @click="pullFromDB()">Update from {{currentSource}}</button>
                </div>
            </div>
            <div class="row w-100 pt-3 mx-auto ">
                <div class="col-md-12 mb-5 px-0">
                    <div class="border rounded" style="min-width:200px">
                        <h5 class="px-3 pt-3">Heart Rate {{heart_rate_date}}</h5>
                        <hr>
                        <div style="position: relative; width:100%">
                            <div id="hr"></div>
                            <div id="hc"></div>
                            <div id="hlf"></div>
                            <div id="hhf"></div>
                       <!--      <div><canvas id="HeartRate" style="position: absolute;"></canvas></div> -->
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-5 px-0">
                    <div class="border rounded" style="height: 350px;min-width:200px">
                        <h5 class="px-3 pt-3">Blood Pressure</h5>
                        <hr>
                        <div style="position: relative;">
                            <div><canvas id="BloodPressure" style="position:absolute;"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-5 px-0">
                    <div class="border rounded" style="height: 350px;min-width:200px">
                        <h5 class="px-3 pt-3">Sleep</h5>
                        <hr>
                        <div style="position: relative;">
                            <div><canvas id="Sleep" style="position:absolute;"></canvas></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-5 px-0">
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
                    from: "",
                    to: "",
                    currentSource: "fitbit",
                    patients: <?= json_encode($pids->patients) ?>,
                    patient: <?= json_encode($patient) ?>,
                    patientPortal: <?= $patientPortal ?>,
                    patient_id: '',
                    heart_rate_date:'',
                    data: {
                        "Heart Rate": [],
                        sleep: []
                    }


                }
            },
            created() {
                //axios.get('localhost')
            },
            methods: {
                async fetchData(url) {
                    return await axios.post(url);
                },
                pullFromSource() {

                },
                baseUrl() {
                    let path = window.location.pathname.split('public')[0];
                    let baseUrl = window.location.origin + path + 'public';
                    return baseUrl;
                },
                async selectPatient() {
                    let byDate = 0;
                    if (this.from != '' && this.to != '') {
                        if (!dateFns.isBefore(this.from, this.to)) {
                            alert('Invalid date interval');
                            return false;
                        }
                        byDate = 1;
                    }

                    let response = await axios.get(this.baseUrl() + '/data.php?pid=' + this.patient_id + '&byDate=' + byDate + '&from=' + this.from + '&to=' + this.to + '&source=' + this.currentSource);
                    if (response.status == 200) {
                        this.data = response.data;
                        console.log(this.data.sleep)
                        this.heartRateChart()
                    }
                },
                async pullFromDB() {
                    if (!dateFns.isBefore(this.from, this.to)) {
                        alert('Invalid date interval');
                        return false;
                    }
                    let response = await axios.get(this.baseUrl() + '/data.php?from=' + this.from + '&to=' + this.to + '&source=' + this.currentSource);
                    if (response.status == 200) {
                        alert(response.data);
                    }
                },
                heartRateChart() {
                    this.heart_rate_date = this.data.heart_rate.date;
                    var options = {
                        series: [{
                            name:"rmssd",
                            data: this.data.heart_rate.rmssd
                        }],
                        chart: {
                            id: 'fb',
                            group: 'social',
                            type: 'line',
                            height: 160
                        },
                        colors: ['#008FFB']
                    };

                    var chart = new ApexCharts(document.querySelector("#hr"), options);
                    chart.render();

                    var optionsLine2 = {
                        series: [{
                            name:'coverage',
                            data: this.data.heart_rate.coverage
                        }],
                        chart: {
                            id: 'tw',
                            group: 'social',
                            type: 'line',
                            height: 160
                        },
                        colors: ['#546E7A']
                    };

                    var chartLine2 = new ApexCharts(document.querySelector("#hc"), optionsLine2);
                    chartLine2.render();

                    var optionsArea = {
                        series: [{
                            name:"low frequency",
                            data:  this.data.heart_rate.coverage
                        }],
                        chart: {
                            id: 'yt',
                            group: 'social',
                            type: 'line',
                            height: 160,
                            tooltip: {
                                shared: false,
                                intersect: true,
                                x: {
                                show: false
                                }
                            },
                        },
                        colors: ['#00E396']
                    };

                    var chartArea = new ApexCharts(document.querySelector("#hlf"), optionsArea);
                    chartArea.render();

                    var optionsSmall = {
                        series: [{
                            name:"high frequency",
                            data: this.data.heart_rate.high_frequency
                        }],
                        chart: {
                            id: 'ig',
                            group: 'social',
                            type: 'line',
                            height: 160,                                                                   
                        },
                        colors: ['#008FFB']
                    };

                    var chartSmall = new ApexCharts(document.querySelector("#hhf"), optionsSmall);
                    chartSmall.render();

                  /*   var optionsSmall2 = {
                        series: [{
                            name:"low frequency",
                            data: this.data.heart_rate.low_frequency
                        }],
                        chart: {
                            id: 'li',
                            group: 'social',
                            type: 'area',
                            height: 160,
                            width: 300
                        },
                        colors: ['#546E7A']
                    };

                    var chartSmall2 = new ApexCharts(document.querySelector("#lf"), optionsSmall2);
                    chartSmall2.render(); */
                },
                initChart(data, labels, id, name) {
                    const ctx = $('#' + id);
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: '# of ' + name,
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
                this.initChart([
                    [12, 19],
                    [3, 5],
                    [2, 3]
                ], ['Red', 'Blue', 'green', 'y'], 'HeartRate', 'Heart Rate')
                this.initChart([12, 19, 3, 5, 2, 3], ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'], 'BloodPressure', 'Blood Pressure')
                this.initChart([12, 19, 3, 5, 2, 3], ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'], 'Sleep', 'Sleep')
                this.initChart([12, 19, 3, 5, 2, 3], ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'], 'Activity', 'Activity')
                let $this = this;
                $(document).ready(function() {
                    $this.$nextTick(function() {

                        //$('.selectpicker').selectpicker('refresh');    
                    })

                })
            }

        }).mount('#appM')

        $(document).ready(function() {
            $('.selectpicker').selectpicker();
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