<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" type="text/css" href="css/normalize.min.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	<link rel="stylesheet" type="text/css" href="css/bootstrap-xlgrid.min.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3"></div>
			<div class="col-md-6">
				<a href="/"><h1 class="text-center">AnimeWalls Dashboard</h1></a>

				<?php
					function no_match($array, $val) {
						foreach ($array as $item) {
							if ($item['name'] == $val) {
								return false;
							} else {
								return true;
							}
						}
					}

					function gen_thumb($url) {
						$im = new Imagick();
						$usmap = $_SERVER['DOCUMENT_ROOT'] . '/' . $url;
						$wp = file_get_contents($usmap);

						$im->readImageBlob($wp);
						$im->setImageFormat("jpeg");
						$im->setImageCompression(Imagick::COMPRESSION_JPEG);
						$im->setImageCompressionQuality(100);
						$im->thumbnailImage(1000, 1000, false, false);

						$im->writeImage($_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('.jpg', '_thumb.jpg', $url));

						$im->clear();
						$im->destroy();
					}

					$file = 'wallpapers.json';
					$json = json_decode(file_get_contents($file), true);
					$catergories = $json['Categories'];

					// New Catergory
					if (isset($_POST["new_category"])) {
						if (no_match($catergories, $_POST["catergory"])) {
							$new_category = array('name' => $_POST["catergory"]);
							array_push($json['Categories'], $new_category);
							file_put_contents($file, json_encode($json));
							echo '<pre>' . var_export($new_category, true) . '</pre>';
						} else {
							echo "Catergory already exists.";
						}
   						
   					}

   					// New Wallpaper
   					if(isset($_POST["new_wallpaper"])) { 						
   						$base_name = preg_replace('/\s+/', '_', strtolower(str_replace(',', '', $_POST['name'])));
			            $ext = pathinfo(basename($_FILES['wallpaper']['name']), PATHINFO_EXTENSION );
			            $wallpaper_name = $base_name . '.' . $ext;
			            $wallpaper_thumb = $base_name . '_thumb.' . $ext;

			            $target = $_SERVER['DOCUMENT_ROOT'] . '/wallpapers/';

			            $counter = 1;
			            while(file_exists($_SERVER['DOCUMENT_ROOT'] . '/wallpapers/' . $wallpaper_name)) {
			            	$wallpaper_name = $base_name . $counter . '.' . $ext;
			            	$wallpaper_thumb = $base_name . $counter . '_thumb.' . $ext;
			            	$counter++;
			            };

			            $target_thumb = $target . $wallpaper_thumb;
			            $target = $target . $wallpaper_name;

			            if (move_uploaded_file($_FILES['wallpaper']['tmp_name'], $target)) {
			            	$target = str_replace('/home/ncollcen/public_html/', 'http://', $target);
			            	$target_thumb = str_replace('/home/ncollcen/public_html/', 'http://', $target_thumb);
			            	gen_thumb('wallpapers/' . $wallpaper_name);
			            	$new_wallpaper = [];
			            	$new_wallpaper['url'] = $target;
			            	$new_wallpaper['catergory'] = $_POST['catergoryW'];
			            	$new_wallpaper['thumbUrl'] = $target_thumb;
			            	$new_wallpaper['name'] = $_POST['name'];
			            	$new_wallpaper['author'] = $_POST['author'];
			            	array_push($json['Wallpapers'], $new_wallpaper);
							file_put_contents($file, json_encode($json));
			            	echo '<pre>' . var_export($new_wallpaper, true) . '</pre>';
			            } else {
			            	echo 'Failed to copy image.';
			            }

   						
   					}

   					// Sort Categories
					$catergories = $json['Categories'];
					asort($catergories);
				
				?>

				<fieldset>
					<h3>Add Catergory</h3>
					<form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
						<div class="form-group">
							<label for="catergory">Catergory</label>
							<input type="text" class="form-control" name="catergory" placeholder="Enter catergory..." required>
						</div>
						<button type="submit" name="new_category" class="btn btn-primary">Submit</button>
					</form>
				</fieldset>

				<fieldset>
					<h3>Add Wallpaper</h3>
					<form role="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
						<div class="form-group">
							<label for="author">Author</label>
							<input type="text" class="form-control" name="author" placeholder="Enter author..." required>
						</div>
						<div class="form-group">
							<label for="name">Name</label>
							<input type="text" class="form-control" name="name" placeholder="Enter name..." required>
						</div>
						<div class="form-group">
							<label for="catergoryW">Catergory</label>
							<select class="form-control" name="catergoryW" required>
								<option value="">None</option>
								<?php
									foreach ($catergories as $c) {
										echo '<option value="' . $c['name'] . '">' . $c['name'] . '</option>';
									}
								?>
							</select>
						</div>
						<div class="form-group">
							<label for="wallpaper">File input</label>
							<input type="file" name="wallpaper" id="wallpaper" required>
						</div>
						<button type="submit" name="new_wallpaper" class="btn btn-primary">Submit</button>
					</form>
				</fieldset>

				<?php 
					//echo '<pre>' . var_export($new, true) . '</pre>';
					// array_push($json['Categories'], $new);
					echo '<pre>' . var_export($json, true) . '</pre>';
					// file_put_contents($file, json_encode($json));
				?>


				
  			</div>
			<div class="col-md-3"></div>
		</div>
	</div>
</body>
</html>