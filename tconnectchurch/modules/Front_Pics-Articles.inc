<?php 
$GeneralItems = new getGeneral( $base_url );
//echo $GeneralItems->getFrontItems( "articles", 12, "imgboard#4", "#4#4OR5OR7" ); 
?>
<div class="front-left">
	<ul class="castingthumblist">
		<li class="list_1">
				    <?php echo $GeneralItems->getFrontItems( "articles", 1, "castpic", "mostrecent#5#4" ); ?>
			</li>
	</ul>
							
							<ul class="column-list">
								<li>
								<a href="<?php echo $base_url; ?>/sermons/pastoral-column" target="_self" title="목회 컬럼">목회 컬럼</a>
								</li>
								<?php echo $GeneralItems->getFrontItems( "articles", 7, "articles", "#4#5"); ?>		
							</ul>
			</div>

			<div class="front-right">
						<ul class="media-list">
							<li>
									<a href="<?php echo $base_url; ?>/sermons/daily-bible-reading" target="_self" title="Read the Bible in a Year">Daily Bible Reading</a>
							</li>
							<?php echo $GeneralItems->getFrontItems( "articles", 3, "qt", "#4#6"); ?>		
							<!--li>
								<div class="date"><a href="#"><span class="day">23</span><span class="month">Jul</span></a></div>
								<div class="bd">
									<h5><a href="#">Eagles Leadership Conference</a></h5>
									<p>Find support and wisdom from Ken Fong and others reflecting together on gender equality in ministry</p>
								</div>
							</li-->
						</ul>
							<ul class="praise-list">
								<li>
								<a href="<?php echo $base_url; ?>/sermons/praise" target="_self" title="목회 컬럼">찬양과 간증</a>
								</li>
								<?php echo $GeneralItems->getFrontItems( "articles", 4, "articles", "#4#14"); ?>		
							</ul>						
			</div>
	</div>
	</section>
<?php 
echo "<section class=\"content\">"
        ."<div class=\"arrow-down-white general-shattered-fixed-banner\">"
		."<div class=\"container-for-arrow\">";
echo $GeneralItems->getFrontItems( "articles", 6, "front_imgboard", "#6#9" ); 
echo "</div></div></section>";
?>

<section class="content">
		<div class="container-with-padding">
					<div class="front-fixed-one">
									<ul class="column-list">
										<li>
												<a href="<?php echo $base_url; ?>/connecting/info-board" target="_self" title="목회 컬럼">커넥트 소식</a>
										</li>
										<?php echo $GeneralItems->getFrontItems( "articles", 7, "articles", "#4#7OR10"); ?>										
									</ul>
					</div>
					<div class="front-fixed-two">
									<ul class="column-list">
										<li>
												<a href="<?php echo $base_url; ?>/connecting/in-life" target="_self" title="목회 컬럼">삶 속 커넥트</a>
										</li>
										<?php echo $GeneralItems->getFrontItems( "articles", 7, "articles", "#4#8"); ?>		
									</ul>
					</div>
					<div class="front-fixed-three">
									<ul class="column-list">
										<li>
												<a href="<?php echo $base_url; ?>mission/domestic-mission" target="_self" title="목회 컬럼">선교 속 커넥트</a>
										</li>
										<?php echo $GeneralItems->getFrontItems( "articles", 7, "articles", "#4#11OR12"); ?>		
									</ul>
					</div>					
		</div>
	</section>