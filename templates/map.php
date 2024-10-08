<?php
declare(strict_types=1);
/** @var string $base_url */
/** @var string $uni */
/** @var string $css */
/** @var string $index_css */
/** @var string $cluster_css */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html" />
		<meta http-equiv="Content-Style-Type" content="text/css" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<title>Pardus Image Map</title>
		<link rel="stylesheet" type="text/css" href="<?= $css; ?>" />
		<link rel="stylesheet" type="text/css" href="<?= $index_css; ?>" />
		<link rel="stylesheet" type="text/css" href="<?= $cluster_css; ?>" />
		<script type="text/javascript" src="<?= $base_url; ?>/resources/main.js"></script>
		<script type="text/javascript">
			function getGemMerchant(uni) {
				var url = "<?= $base_url ?>/info/gemmerchant.php";
				var params = "uni=" + uni;
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				//xmlhttp.setRequestHeader("Content-length", params.length);
				//xmlhttp.setRequestHeader("Connection" , "close");
				
				xmlhttp.onreadystatechange = function () {
					if (xmlhttp.readyState == 4) {
						var el = document.getElementById("gem_merchant");
						el.innerHTML = xmlhttp.responseText;
					}
				}
				
				xmlhttp.send(params);
			}
			var xmlhttp = getXMLHttpObject();
			var overviewhttp = getXMLHttpObject();
			<?php if (isset($gems)) {
				echo "window.onload = getGemMerchant('" . $uni . "');";
			} ?>
			
			function getSectors(uni) {
				var url = "<?= $base_url ?>/info/sectorlist.php";
				var params = "uni=" + uni;
				xmlhttp.open("POST",url,true);
				xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
				//xmlhttp.setRequestHeader("Content-length", params.length);
				//xmlhttp.setRequestHeader("Connection" , "close");
				
				xmlhttp.onreadystatechange = function () {
					if (xmlhttp.readyState == 4) {
						var el = document.getElementById("gem_merchant");
						el.innerHTML = xmlhttp.responseText;
					}
				}
				
				xmlhttp.send(params);
			}
			var xmlhttp = getXMLHttpObject();
			var overviewhttp = getXMLHttpObject();
			<?php if (isset($_REQUEST['sectors'])) {
				echo "window.onload = getSectors('" . $uni . "');";
			} ?>
		</script>
		<script type="text/javascript">

			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-15475436-5']);
			_gaq.push(['_setDomainName', '.mhwva.net']);
			_gaq.push(['_trackPageview']);
	
			(function() {
				var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
				ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
				var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
			})();

		</script>	
	</head>
	<body>
		<div id="imgmap">
			<div id="header_side"><?php require_once(templates('header_side')); ?></div>
			<div id="imgmap-img" onmouseover="this.style.zIndex=200" onmouseout="this.style.zIndex=0">
				<a href="<?= $base_url . '/' . $uni; ?>/FSH" id="fsh"><i>FSH</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/GAP" id="gap"><i>GAP</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/UNR" id="unr"><i>UNR</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/LANE" id="lane"><i>LANE</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/URC" id="urc"><i>URC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/UKC" id="ukc"><i>UKC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/FHC" id="fhc"><i>FHC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/FRC" id="frc"><i>FRC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/NPR" id="npr"><i>NPR</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/WPR" id="wpr"><i>WPR</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/EPR" id="epr"><i>EPR</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/SPLIT" id="split"><i>SPLIT</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/EKC" id="ekc"><i>EKC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/ESC" id="esc"><i>ESC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/EWS" id="ews"><i>EWS</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/SPR" id="spr"><i>SPR</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/CORE" id="puc"><i>PUC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/CORE" id="pfc"><i>PFC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/CORE" id="pec"><i>PEC</i></a>
				<a href="<?= $base_url . '/' . $uni; ?>/CORE" id="pc"><i>PC</i></a>
				<a href="<?= $base_url; ?>/index.php" id="home"><i>HOME</i></a>
				<a href="https://www.pardus.at" id="pardus"><i>PARDUS</i></a>
			</div>
			<div id="cluster-map">
				<?php if (isset($cluster)) { 
                    $url = rtrim((string) $base_url) . '/' . $uni . '/';
                    require_once(clusters(strtolower((string) $cluster))); 
                } ?>
			</div>
			<div id="gem_merchant"></div>
			<div id="overview" name="gem"></div>
		</div>
		<div id="footer"><center><?php require_once(templates('footer')); ?></center></div>
	</body>
</html>
