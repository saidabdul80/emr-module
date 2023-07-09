<head>
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
    <style>
        .cdc div::-webkit-scrollbar {
            width: 3px;
            height: 5px;

        }

        .cdc div::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .cdc div::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .cdc div::-webkit-scrollbar-thumb:hover {

            background: #555;
        }
    </style>
</head>

<body class="p-0 mx-auto">
    <div id="loader" class="w-100 position-absolute">
        <div class="spinner-border" role="status" style="width: 60px;height:60px">
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-lg w-100">
        <a class="navbar-brand" href="#">PGHD</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="sample-index.php">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="auth_page.php" >Wearables</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Pricing</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="settings.php">Settings</a>
                </li>
            </ul>
        </div>
    </nav>