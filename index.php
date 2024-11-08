<!DOCTYPE html>
<html lang="en">

<head> 
    <meta charset="utf-8"> 
    <meta name="viewport" content="width=device-width, initial-scale=1">  
    <title>KABUPATEN SLEMAN</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"  
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            width: 100%;
            height: 600px;
        }

        main {
            margin-top: 80px;
        }

        /* Tabel warna pink tua dan muda */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #ffb6c1; /* Light pink */
        }

        .table-striped tbody tr:nth-of-type(even) {
            background-color: #ff69b4; /* Hot pink */
        }

        .table thead th {
            background-color: #ff1493; /* Deep pink */
            color: white;
        }

        .alert-warning {
            background-color: #ffcccb; /* Light pink for alerts */
        }

        /* Pink border for container and cards */
        .border-warning {
            border-color: #ff1493 !important; /* Deep pink for the border */
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">KABUPATEN SLEMAN</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#penduduk">Penduduk</a></li>
                    <li class="nav-item"><a class="nav-link" href="#peta">Peta</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kritiksaran">Kritik dan Saran</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#exampleModal">Pembuat</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <div class="container border border-warning rounded">
            <div class="alert alert-warning text-center" role="alert">
                <h1>KABUPATEN SLEMAN</h1>
                <h4>Provinsi Daerah Istimewa Yogyakarta</h4>
            </div>

            <!-- Section Peta -->
            <div class="card mt-4 border border-warning">
                <div class="card-header alert alert-warning">
                    <h4 id="peta">Peta</h4>
                </div>
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>


            
            <!-- Section Penduduk -->
            <div class="card mt-4 border border-warning">
                <!-- Section Penduduk -->
                <div class="card mt-4 border border-warning">
                <div class="card-header alert alert-warning">
                    <h4 id="penduduk">Data Penduduk</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr class="text-center">
                                <th>Kecamatan</th>
                                <th>Latitude</th>
                                <th>Longitude</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $servername = "localhost";
                            $username = "root";
                            $password = "";
                            $dbname = "pgweb8";

                            $conn = new mysqli($servername, $username, $password, $dbname);
                            if ($conn->connect_error) {
                                die("Connection failed: " . $conn->connect_error);
                            }

                            // Handle delete action
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_kecamatan'])) {
                                $deleteKecamatan = $_POST['delete_kecamatan'];
                                $deleteSql = "DELETE FROM tabel_penduduk WHERE Kecamatan = ?";
                                $stmt = $conn->prepare($deleteSql);
                                $stmt->bind_param("s", $deleteKecamatan);
                                $stmt->execute();
                                $stmt->close();
                            }

                            // Handle edit action
                            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_kecamatan'])) {
                                $editKecamatan = $_POST['edit_kecamatan'];
                                $newLatitude = $_POST['new_latitude'];
                                $newLongitude = $_POST['new_longitude'];
                                $updateSql = "UPDATE tabel_penduduk SET Latitude = ?, Longitude = ? WHERE Kecamatan = ?";
                                $stmt = $conn->prepare($updateSql);
                                $stmt->bind_param("dds", $newLatitude, $newLongitude, $editKecamatan);
                                $stmt->execute();
                                $stmt->close();
                            }

                            $sql = "SELECT Kecamatan, Latitude, Longitude FROM tabel_penduduk";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . $row["Kecamatan"] . "</td>
                                            <td>" . $row["Latitude"] . "</td>
                                            <td>" . $row["Longitude"] . "</td>
                                            <td>
                                                <form method='post' style='display:inline;'>
                                                    <input type='hidden' name='delete_kecamatan' value='" . $row["Kecamatan"] . "'>
                                                    <button type='submit' class='btn btn-danger btn-sm'>Hapus</button>
                                                </form>
                                                <button type='button' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal' onclick='setEditData(\"" . $row["Kecamatan"] . "\", \"" . $row["Latitude"] . "\", \"" . $row["Longitude"] . "\")'>Edit</button>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center'>Tidak ada data</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal for Editing Data -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Data Penduduk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="edit_kecamatan" class="form-label">Kecamatan</label>
                                <input type="text" class="form-control" id="edit_kecamatan" name="edit_kecamatan" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="new_latitude" class="form-label">Latitude</label>
                                <input type="text" class="form-control" id="new_latitude" name="new_latitude" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_longitude" class="form-label">Longitude</label>
                                <input type="text" class="form-control" id="new_longitude" name="new_longitude" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

            <!-- Kritik dan Saran Section -->
            <div class="card mt-4 border border-warning">
                <div class="card-header alert alert-warning">
                    <h4 id="kritiksaran">Kritik dan Saran</h4>
                </div>
                <div class="card-body">
                    <form action="#" method="post">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="nama" name="nama" placeholder="Isikan nama Anda">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="kritik" class="form-label">Kritik dan saran</label>
                            <textarea class="form-control" id="kritik" name="kritik" rows="3"></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Kirim</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Section -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Pembuat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Nama: Alifa Salwa Salsabillah Rangkuti<br>
                            NIM: 23/516044/SV/22669<br>
                            Kelas: A
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <script>
        // Initialize map
        var map = L.map("map").setView([-7.7681, 110.296], 12);
        
        // Add base map layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        <?php
        // Display markers on map
        $conn = new mysqli($servername, $username, $password, $dbname);
        $sql = "SELECT Kecamatan, Latitude, Longitude FROM tabel_penduduk";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lat = $row["Latitude"];
                $long = $row["Longitude"];
                $info = $row["Kecamatan"];
                echo "L.marker([$lat, $long]).addTo(map).bindPopup('$info');";
            }
        }
        $conn->close();
        ?>
    </script>
</body>
</html>
