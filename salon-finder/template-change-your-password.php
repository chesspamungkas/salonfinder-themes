<?php
 /* Template Name: Change Password*/ 
get_header();?>
<div class="col-md-12 thanks_page text-center mt-5 mb-5">
    <h2 class="poppins-bold">Password changed successfully!</h2>
    <p id="time" class="poppins-medium"></p>
</div>
<script language="JavaScript" type="text/javascript">
var seconds = 3;
var url="<?php bloginfo('url');?>/profile";

function redirect(){
    if (seconds <=0){
        // redirect to new url after counter  down.
        window.location = url;
    }else{
        seconds--;
        document.getElementById("time").innerHTML = "You will be redirected to your profile in "+seconds+" seconds."
        setTimeout("redirect()", 1000)
    }
}
 redirect();
</script>
<?php get_footer();?>