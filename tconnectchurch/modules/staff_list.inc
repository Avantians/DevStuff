<ul class="staff_section">
<?php
$count = 1;
$mquery = $Bon_db->getQuery("SELECT * FROM staff WHERE publish = '1' AND status = '1' ORDER BY ordering");
while( $_menus5 = $Bon_db->getFetch_Array($mquery) ){

	echo "<li><img src='".$base_url."/upload/".$_menus5['filename']."' alt='".$_menus5['name']."' width='150'><strong class=\"korea-txt\">".$_menus5['name']."</strong>&nbsp;&nbsp;|&nbsp;&nbsp;".$_menus5['jobtitle']."<br/><a href='mailto:".$_menus5['email']."' title='Email to ".$_menus5['name']."' target='_blank'>".$_menus5['email']."</a>".$_menus5['fulltxt']."</li>\n";
		$count++;
}
?></ul>