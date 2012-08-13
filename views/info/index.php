<?php include_once 'header.php';?>
<?php
  $infoModels=$dblink->findAll("info_posts");
  // echo "<pre>";
  // print_r($infoModels);
?>
<!-- recommend info -->
<div class="hero-unit">
<h1>这是一个信息系统首页</h1>
<p>这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页这是一个信息系统首页</p>
<p><a class="btn btn-primary btn-large" href="info_detail.html">查看详细 &raquo;</a></p>
</div>
<!-- recommend info End -->
<!-- info list -->
<div class="row">
<?php 
  if($infoModels){
    foreach ($infoModels as $val) {
?>
  <div class="span4">
    <h2><?=$val['post_title']?></h2>
    <p><?=$val['post_content']?></p>
    <p><a class="btn" href="#">查看详细 &raquo;</a></p>
  </div>
<?php
    }
  }else{
?>
  <div class="span12">
    <h2><?="暂无信息.."?></h2>
  </div>
<?php
  }
?>

</div>
<!-- info list End -->
<?php include_once 'footer.php';?>