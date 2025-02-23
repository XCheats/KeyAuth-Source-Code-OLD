<?php
ob_start();

include '../../../includes/connection.php'; // db config
include '../../../includes/functions.php'; // core funcs
session_start();

if (!isset($_SESSION['username'])) // if user not logged in, send them to login
{
    header("Location: ../../../login/");
    exit();
}

$username = $_SESSION['username']; // current user
($result = mysqli_query($link, "SELECT * FROM `accounts` WHERE `username` = '$username'")) or die(mysqli_error($link)); // get user info
$row = mysqli_fetch_array($result);

$isbanned = $row['isbanned'];
if ($isbanned == "1") // kill session for banned users
{
				echo "<meta http-equiv='Refresh' Content='0; url=../../../login/'>"; 
				session_destroy();
				exit();
}

$role = $row['role']; // get user role to determine appropriate permissions
$_SESSION['role'] = $role;

if ($role == "Reseller") // block resellers, else they could create as many keys as they want, or manage application in ways that shouldn't be authorized to them
{
    die('Resellers Not Allowed Here');
}

$darkmode = $row['darkmode']; // get darkmode setting to later use when rendering HTML

?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>KeyAuth - Blacklists</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../../../static/images/favicon.png">
	<script src="../../files/assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Custom CSS -->
	<link href="../../files/assets/extra-libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <link href="../../files/assets/libs/chartist/dist/chartist.min.css" rel="stylesheet">
    <link href="../../files/assets/extra-libs/c3/c3.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../files/dist/css/style.min.css" rel="stylesheet">
	

	<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<?php


if (!$_SESSION['app']) // no app selected yet
{
    

    $result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '" . $_SESSION['username'] . "'"); // select all apps where owner is current user
    if (mysqli_num_rows($result) > 0) // if the user already owns an app, proceed to change app or load only app
    {

        if (mysqli_num_rows($result) == 1) // if the user only owns one app, load that app (they can still change app after it's loaded)
        {
            $row = mysqli_fetch_array($result);
            $_SESSION['name'] = $row["name"];
            $_SESSION['app'] = $row["secret"];
            $_SESSION['secret'] = $row["secret"];
            echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#content").fadeIn(1900);
        $("#sticky-footer bg-white").fadeIn(1900);
        });             
                
                </script>
                ';
        }
        else // otherwise if the user has more than one app, choose which app to load
        {
            echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#changeapp").fadeIn(1900);
        });             
                
                </script>
                ';
        }
    }
    else // if user doesnt have any apps created, take them to the screen to create an app
    
    {
        echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#createapp").fadeIn(1900);
        });             
                
                </script>
                ';
    }

}
else // app already selected, load page like normal

{
    echo '
                <script type=\'text/javascript\'>
                
                        $(document).ready(function(){
        $("#content").fadeIn(1900);
        $("#sticky-footer bg-white").fadeIn(1900);
        });             
                
                </script>
                ';
}

?>
</head>
<body data-theme="<?php if ($darkmode == 0)
{
    echo "dark";
}
else
{
    echo "light";
} ?>">

    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin1" data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <header class="topbar" data-navbarbg="skin1">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin5">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                    <!-- ============================================================== -->
                    <!-- Logo -->
                    <!-- ============================================================== -->
                    <a class="navbar-brand">
                        <!-- Logo icon -->
                        <b class="logo-icon">
                            <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                            <!-- Dark Logo icon -->
                            <img src="../../files/assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            <!-- Light Logo icon -->
                            <img src="../../files/assets/images/logo-light-icon.png" alt="homepage" class="light-logo" />
                        </b>
                        <!--End Logo icon -->
                        <!-- Logo text -->
                        <span class="logo-text">
                             <!-- dark Logo text -->
                             <img src="../../files/assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                             <!-- Light Logo text -->    
                             <img src="../../files/assets/images/logo-light-text.png" class="light-logo" alt="homepage" />
                        </span>
                    </a>
                    <!-- ============================================================== -->
                    <!-- End Logo -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Toggle which is visible on mobile only -->
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><i class="ti-more"></i></a>
                </div>
                <!-- ============================================================== -->
                <!-- End Logo -->
                <!-- ============================================================== -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin1">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item d-none d-md-block"><a class="nav-link sidebartoggler waves-effect waves-light" href="javascript:void(0)" data-sidebartype="mini-sidebar"><i class="mdi mdi-menu font-24"></i></a></li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav">
                        <!-- ============================================================== -->
                        <!-- create new -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://keyauth.com/discord/" target="discord"> <i class="mdi mdi-discord font-24"></i>
						</a>
						</li>
						<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle waves-effect waves-dark" href="https://t.me/KeyAuth" target="telegram"> <i class="mdi mdi-telegram font-24"></i>
						</a>
						</li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $_SESSION['img']; ?>" alt="user" class="rounded-circle" width="31"></a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow"><span class="bg-primary"></span></span>
                                <div class="d-flex no-block align-items-center p-15 bg-primary text-white mb-2">
                                    <div class=""><img src="<?php echo $_SESSION['img']; ?>" alt="user" class="img-circle" width="60"></div>
                                    <div class="ml-2">
                                        <h4 class="mb-0"><?php echo $_SESSION['username']; ?></h4>
                                        <p class=" mb-0"><?php echo $_SESSION['email']; ?></p>
                                    </div>
                                </div>
                                <a class="dropdown-item" href="../../account/logs/"><i class="mdi mdi-folder-account font-18"></i> Account Logs</a>
                                <a class="dropdown-item" href="../../account/settings/"><i class="ti-settings mr-1 ml-1"></i> Account Settings</a>
                                <a class="dropdown-item" href="../../account/logout/"><i class="fa fa-power-off mr-1 ml-1"></i> Logout</a>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <aside class="left-sidebar" data-sidebarbg="skin5">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <?php
sidebar($role); // display navigation menu, it's in funcs now so that it can be modified once for all the pages

if (isset($_POST['addblack']))
{
    $data = sanitize($_POST['blackdata']);
    $type = sanitize($_POST['blacktype']);

	// I should probably change this to a switch statement
    if ($type == "IP Address")
    {
        $result = mysqli_query($link, "INSERT INTO `bans`(`ip`, `type`, `app`) VALUES ('$data','ip','" . $_SESSION['app'] . "')");
        if ($result)
        {

            echo "<meta http-equiv='Refresh' Content='2;'>";
            success("Created Blacklist!");
        }
    }
    else if ($type == "Hardware ID")
    {
        $result = mysqli_query($link, "INSERT INTO `bans`(`hwid`, `type`, `app`) VALUES ('$data','hwid','" . $_SESSION['app'] . "')");
        if ($result)
        {

            echo "<meta http-equiv='Refresh' Content='2;'>";
            success("Created Blacklist!");
        }
    }

}

if (isset($_POST['delblacks']))
{
    $result = mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . $_SESSION['app'] . "'");
    if ($result)
    {
        echo "<meta http-equiv='Refresh' Content='2;'>";
        success("Deleted All Blacklists!");
    }
}
?>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Blacklists</h4>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
			
			<div class="main-panel" id="createapp" style="padding-left:30px;display:none;">
             <!-- Page Heading -->
             <br>
                    <h1 class="h3 mb-2 text-gray-800">Create an App</h1>
                    <br>
                    <br>
                    <form method="POST" action="">
   <input type="text" id="appname" name="appname" class="form-control" placeholder="Application Name..."></input>
  <br>
  <br>
   <button type="submit" name"ccreateapp" class="btn btn-primary" style="color:white;">Submit</button>
   </form>
        </div>
        
			
			<div class="main-panel" id="changeapp" style="padding-left:30px;display:none;">
             <!-- Page Heading -->
             <br>
                    <h1 class="h3 mb-2 text-gray-800">Choose an App</h1>
                    <br>
                    <br>
                    <form class="text-left" method="POST" action="">
<select class="form-control" name="taskOption">
        <?php
$username = $_SESSION['username'];
($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `owner` = '$username'")) or die(mysqli_error($link));
if (mysqli_num_rows($result) > 0)
{
    while ($row = mysqli_fetch_array($result))
    {
        echo "  <option>" . $row["name"] . "</option>";
    }
}

?>
</select>    

  <br>
  <br>
   <button type="submit" name="change" class="btn btn-primary" style="color:white;">Submit</button><a style="padding-left:5px;color:#4e73df;" id="createe">Create Application</a>
   </form>
   <script type="text/javascript">

var myLink = document.getElementById('createe');

myLink.onclick = function(){


$(document).ready(function(){
        $("#changeapp").fadeOut(100);
        $("#createapp").fadeIn(1900);
        }); 

}


</script>
   <?php
if (isset($_POST['change']))
{
    $selectOption = sanitize($_POST['taskOption']);
    ($result = mysqli_query($link, "SELECT * FROM `apps` WHERE `name` = '$selectOption' AND `owner` = '" . $_SESSION['username'] . "'")) or die(mysqli_error($link));
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {
            $secret = $row["secret"];
            $sellerkey = $row["sellerkey"];
        }
    }
    else
    {
        mysqli_close($link);
        error("You don\'t own application!");
        echo "<meta http-equiv='Refresh' Content='2'>";
		return;
    }
    $_SESSION['secret'] = $secret;
    $_SESSION['app'] = $secret;
    $_SESSION['name'] = $selectOption;
    $_SESSION['sellerkey'] = $sellerkey;

    success("You have changed Applications!");
    echo "<meta http-equiv='Refresh' Content='2;'>";
}
?>
   </div>
   
            <!-- ============================================================== -->
            <div class="container-fluid" id="content" style="display:none;">
                <!-- ============================================================== -->
                <!-- Start Page Content -->
                <!-- ============================================================== -->
                <!-- File export -->
                <div class="row">
                    <div class="col-12">
					<?php heador($role, $link); // display app info and buttons to change, delete, and pause app ?>
					<form method="post">
					<button data-toggle="modal" type="button" data-target="#create-blacklist" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-plus-circle fa-sm text-white-50"></i> Create Blacklist</button>  <button name="delblacks" onclick="return confirm('Are you sure you want to delete all blacklists?')" class="dt-button buttons-print btn btn-primary mr-1"><i class="fas fa-trash-alt fa-sm text-white-50"></i> Delete All Blacklists</button></form>
							<br>
							<div class="alert alert-info alert-rounded">Please watch tutorial video if confused <a href="https://youtube.com/watch?v=1lHjDeB3dA0" target="tutorial">https://youtube.com/watch?v=1lHjDeB3dA0</a> You may also join Discord and ask for help!
                                        </div>
<div id="create-blacklist" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Add Blacklist</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">x</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Blacklist Type:</label>
                                                        <select name="blacktype" class="form-control"><option>IP Address</option><option>Hardware ID</option></select>
                                                    </div>
													<div class="form-group">
                                                        <label for="recipient-name" class="control-label">Blacklist Data:</label>
                                                        <input type="text" class="form-control" placeholder="IP or HWID to blacklist" name="blackdata" required>
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="addblack">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>
									
									<div id="rename-app" class="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header d-flex align-items-center">
												<h4 class="modal-title">Rename Application</h4>
                                                <button type="button" class="close ml-auto" data-dismiss="modal" aria-hidden="true">×</button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="post">
                                                    <div class="form-group">
                                                        <label for="recipient-name" class="control-label">Name:</label>
                                                        <input type="text" class="form-control" name="name" placeholder="New Application Name">
                                                    </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
                                                <button class="btn btn-danger waves-effect waves-light" name="renameapp">Add</button>
												</form>
                                            </div>
                                        </div>
                                    </div>
									</div>

<script type="text/javascript">

var myLink = document.getElementById('mylink');

myLink.onclick = function(){


$(document).ready(function(){
        $("#content").fadeOut(100);
        $("#changeapp").fadeIn(1900);
        }); 

}


</script>
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="file_export" class="table table-striped table-bordered display">
                                        <thead>
                                            <tr>
<th>Blacklist Data</th>
<th>Blacklist Type</th>
<th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
<?php
if ($_SESSION['app'])
{
    ($result = mysqli_query($link, "SELECT * FROM `bans` WHERE `app` = '" . $_SESSION['app'] . "'")) or die(mysqli_error($link));
    if (mysqli_num_rows($result) > 0)
    {
        while ($row = mysqli_fetch_array($result))
        {

            echo "<tr>";

            $data = $row["hwid"] ?? $row["ip"]; // display either hwid or IP, depending which one isn't null
            echo "  <td>" . $data . "</td>";

            echo "  <td>" . $row["type"] . "</td>";

            // echo "  <td>". $row["status"]. "</td>";
            echo '<td><button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Manage
                                            </button>
                                            <div class="dropdown-menu"><form method="post">
                                                <button class="dropdown-item" name="deleteblack" value="' . $data . '">Delete</button><input type="hidden" name="type" value="' . $row["type"] . '"></div></td></tr></form>';

        }

    }

}

?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
<th>Blacklist Data</th>
<th>Blacklist Type</th>
<th>Action</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Show / hide columns dynamically -->
                
                <!-- Column rendering -->
                
                <!-- Row grouping -->
                
                <!-- Multiple table control element -->
                
                <!-- DOM / jQuery events -->
                
                <!-- Complex headers with column visibility -->
                
                <!-- language file -->
                
                <!-- Setting defaults -->
                
                <!-- Footer callback -->
                
                <?php
if (isset($_POST['deleteblack']))
{
    $blacklist = sanitize($_POST['deleteblack']);
    $type = sanitize($_POST['type']);
    if ($type == "ip")
    {
        mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . $_SESSION['app'] . "' AND `ip` = '$blacklist'");
    }
    elseif ($type == "hwid")
    {
        mysqli_query($link, "DELETE FROM `bans` WHERE `app` = '" . $_SESSION['app'] . "' AND `hwid` = '$blacklist'");
    }

    if (mysqli_affected_rows($link) != 0)
    {
        success("Blacklist Successfully Deleted!");
        echo "<meta http-equiv='Refresh' Content='2'>";
    }
    else
    {
        mysqli_close($link);
        error("Failed To Delete Blacklist!");
    }
}
?>
                
                <!-- ============================================================== -->
                <!-- End PAge Content -->
                <!-- ============================================================== -->
                <!-- ============================================================== -->
                <!-- Right sidebar -->
                <!-- ============================================================== -->
                <!-- .right-sidebar -->
                <!-- ============================================================== -->
                <!-- End Right sidebar -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <footer class="footer text-center">
       Copyright &copy; <script>document.write(new Date().getFullYear())</script> KeyAuth
</footer>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    
   
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../../files/assets/libs/popper-js/dist/umd/popper.min.js"></script>
    <script src="../../files/assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script src="../../files/dist/js/app.min.js"></script>
    <script src="../../files/dist/js/app.init.dark.js"></script>
    <script src="../../files/dist/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../../files/assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../../files/assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="../../files/dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="../../files/dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
   <script src="../../files/dist/js/feather.min.js"></script>
    <script src="../../files/dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <!--chartis chart-->
    <script src="../../files/assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="../../files/assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <!--c3 charts -->
    <script src="../../files/assets/extra-libs/c3/d3.min.js"></script>
    <script src="../../files/assets/extra-libs/c3/c3.min.js"></script>
    <!--chartjs -->
    <script src="../../files/assets/libs/chart-js/dist/chart.min.js"></script>
    <script src="../../files/dist/js/pages/dashboards/dashboard1.js"></script>
		<script src="../../files/assets/extra-libs/datatables.net/js/jquery.dataTables.min.js"></script>
	    <!-- start - This is for export functionality only -->
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
  
					

<script src="../../files/dist/js/pages/datatable/datatable-advanced.init.js"></script>
</body>
</html>