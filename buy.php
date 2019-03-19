<html>
<head><title>Buy Products</title></head>
<style>
table {

    width: 100%;
}

td, th {
    padding: 8px;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}
tr:nth-child(odd) {
    background-color: #bfbfbf;
}
</style>
<body >
<center><h3>Shopping Basket:</h3></center>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors','On');
$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey='Your API KEY here'&trackingId=7000610&keyword=samsung+i7');
$xml = new SimpleXMLElement($xmlstr);
class shopping_cart {
	var $p_id;
	var $p_name;
	var $p_price;
	public function __construct($p_id,$p_name,$p_price,$p_description){
		$this->id = $p_id;
		$this->name = $p_name;
		$this->price = $p_price;
		$this->description = $p_description;
	}
}
if(isset($_GET['buy']))
{
 if(isset($_SESSION['search_display'])){
 	foreach ($_SESSION['search_display'] as $obj) {
 		if($obj["id"]==$_GET['buy']){
 			$_SESSION['cart_added'][$_GET['buy']]= $obj;	
 		}
 	}
 }
 $total_price=0;
}

if(isset($_GET['delete'])){
	$rem_prod_id=$_GET['delete'];
	foreach ($_SESSION['cart_added'] as $rem_obj) {
		if($rem_prod_id == $rem_obj["id"] ){
			unset($_SESSION['cart_added'][$_GET['delete']]);
		}
	}
}
if(isset($_GET['clear'])){
	unset($_SESSION['cart_added']);
	$total_price=0;
}
?>
<?php
$total_price=0;
	if(isset($_SESSION['cart_added'])){
		foreach($_SESSION['cart_added'] as $cartObject){
				echo "<table border=1 style:width='100%''>";
				echo "<tr>";
				echo "<td>".$cartObject["name"]."</td>";
				echo "<td>".$cartObject["price"]."</td>";
				echo "<td><a href = 'buy.php?delete=".$cartObject["id"]."'>Delete</a></td>";
				echo "</tr>";				
				$total_price+=$cartObject["price"];
			}		
	}   echo "</table><br>";
		echo "<b> Total: ".$total_price."$ </b><br><br>";
		echo "<form action='buy.php' method='GET'> <input type='hidden' name='clear' value='1'> <input type='submit' value='Empty Basket'> </form>";
?>
<form action="buy.php" method="GET">
		<fieldset>
			<legend>Find Products:</legend>
			<label>Category:
				<select name="category" value="72">
					<?php
					$xmlstr1 = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey='Your API KEY here'&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
					//header('Content-Type: text/xml');
					$xml1 = new SimpleXMLElement($xmlstr1);
					echo "<option value='".$xml1->category['id']."' selected='selected'>".$xml1->category->name."</option>";
					foreach ($xml1->category as $c)
						foreach ($c->categories as $d)
							foreach ($d->category as $n){
								echo "<optgroup label='".$n->name."'>";
								foreach ($n->categories as $a)
									foreach ($a->category as $p)
										echo "<option value='".$p['id']."'>".$p->name."</option>";
								echo "</optgroup>";
							}								
					?>	
				</select>
			</label>
			<label> Search Keywords :
				<input type="text" name="search"></input>
			</label>
			<input type="submit" value="Search"></input>
		</fieldset>
	</form>
	<?php
			$_SESSION['search_display'] = array();
			if(isset($_GET['category']) && isset($_GET['search'])){
				$c_id = $_GET['category'];
				$search = $_GET['search'];
				$xmlstr2 = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey='Your API KEY here'&trackingId=7000610&categoryId='.$c_id.'&keyword='.$search.'&numItems=20');
				$xml2 = new SimpleXMLElement($xmlstr2);
				foreach($xml2->categories as $test){
					if($test['matchedCategoryCount'] == 0){
						echo "<b>No matches found</b>";
					}
					else{
						echo "<table border=1 style='width:100%'>";
						echo "<tr>";
						echo "<th> Name </th>";
						echo "<th> Price </th>";
						echo "<th> Description </th>";
						echo "</tr>";
						foreach($test->category->items->offer as $o){
						echo "<tr>";
						echo "<td><a href='buy.php?buy=".$o['id']."'>".$o->name."</a></td>";
						echo "<td>".$o->basePrice."</td>";
						echo "<td>".$o->description."</td>";
						echo "</tr>";
						$p_id=(string)$o['id'];
						$p_name=(string)$o->name;
						$p_price= (string)$o->basePrice;
						$p_description=(string)$o->description;
						$cart_obj = new shopping_cart($p_id,$p_name,$p_price,$p_description);
						$_SESSION['search_display'] []=(array)$cart_obj;
				}
				echo "</table>";
				}
			}
					}					
			
	?>		
	
</body>
</html>
