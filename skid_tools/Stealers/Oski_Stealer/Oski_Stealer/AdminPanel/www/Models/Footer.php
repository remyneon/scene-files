<?

// -------------------------------------------------------------------------
// auth checker
if($_SESSION["login"] != true)
{
	header('Location: login.php', true, 301);
	exit();
}

?>

<script src="https://unpkg.com/feather-icons"></script>

<script src="Template/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="Template/lib/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="Template/lib/chart.js/Chart.bundle.min.js"></script>
<script src="Template/lib/jquery.flot/jquery.flot.js"></script>
<script src="Template/lib/jquery.flot/jquery.flot.stack.js"></script>
<script src="Template/lib/jquery.flot/jquery.flot.resize.js"></script>
<script src="Template/jszip.min.js"></script>
<script src="Template/FileSaver.min.js"></script>
<script src="Template/jszip-utils.min.js"></script>