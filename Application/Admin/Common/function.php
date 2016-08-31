<?php
function is_login(){
    return session("uid") ? 1 : 0;
}
?>