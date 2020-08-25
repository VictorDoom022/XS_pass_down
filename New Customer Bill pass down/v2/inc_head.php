<?php
  $disableRightClick="<script language=\"JavaScript\" type=\"text/javascript\" >	\n  "
    ."                                                       	\n  "
    ." document.oncontextmenu = function(){return false}         	\n  "
    ."                                                           	\n  "
    ." if(document.layers) {                                     	\n  "
    ." 	window.captureEvents(Event.MOUSEDOWN);               	\n "
    ." 	window.onmousedown = function(e){                    	\n "
    ." 		if(e.target==document)                       	\n "
    ." 			return false;                        	\n "
    ." 	}                                                    	\n "
    ." }                                                         	\n  "
    ." else {                                                    	\n  "
    ." 	document.onmousedown = function(e){      	\n "
    ." 		e = window.event;             					\n "
    ." 			if(e.which == undefined){               					\n "
    ." 				return false                   							\n "
    ." 			}else{                 							\n "
    ." 				if(e.which ==3){               							\n "
    ." 					return false                 							\n "
    ." 				}                 							\n "
    ." 			}                							\n "
    ." 	}               							\n "
    ." }                                                         	\n  "
    ." </script>                                                 	\n  "
    ;
  
  //Session information of right click disabling
  if(!$_SESSION['controlRC']){
    echo $disableRightClick;
  }
?>