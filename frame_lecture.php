<?php
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['id_user']) || $_SESSION['id_user'] == "")
{
  exit();
}
echo "<html><head></head>";
echo "<body background='white' marginwidth='0' marginheight='0' leftmargin='0' topmargin='0'>";
?>
</body>
</html>
