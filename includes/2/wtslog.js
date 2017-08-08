

function wtslog(uid,dbn,ssl,page_name,invisible,text_counter){


   if (! page_name || page_name == '#'){
      page_name = '';
   }

   if (! invisible || invisible == '#'){
      invisible = '';
   }

   if (! text_counter || text_counter == '#'){
      text_counter = 'no';
   }

   if (ssl == 'https'){
      var prefix = 'https';
   }
   else {
      var prefix = 'http';
   }

   if (dbn == 1){
      var prefix = prefix+'://server2.web-stat.com';
   }
   else if (dbn == 2){
      var prefix = prefix+'://server3.web-stat.com';
   }
   else if (dbn == 3){
      var prefix = prefix+'://server4.web-stat.com';
   }
   else if (dbn == 4){
      var prefix = prefix+'://server2.web-stat.com';
   }
   else {
      var prefix = prefix+'://server2.web-stat.com';
   }


   try{var wtsb=top.document;var wtsr=wtsb.referrer;var wtsu=wtsb.URL;}
   catch(e){var wtsb=document;var wtsr=wtsb.referrer;var wtsu=wtsb.URL;}

   var qry= uid+':'+dbn+'::'+escape(wtsr)+'::'
+screen.width+'x'+screen.height+'::'+screen.colorDepth+'::'
+escape(page_name)+'::'+invisible+'::'+Math.random()+'::'+escape(wtsu)+'::'+document.title;

   if (invisible == 'event_track'){
      document.wtscount.src=eval("prefix+'/count.pl?'+qry");
      pausecomp(2000);
      return;
   }

   if (text_counter == 'yes' || text_counter == 'no_count'){
      document.write('<script language="JavaScript" src="'+prefix+'/count_text.pl?'+qry+'::'+text_counter+'"></script>');

   }
   else {

      document.write('<a href="http://www.web-stat.com/stats/'+uid+'.htm" ');
      document.write('target="new"><span id="wtsdiv" style="text-decoration:none;"><img id="wtscount" name="wtscount" src="'+prefix+'/count.pl?');

      if (invisible == 'yes'){
         document.write(qry+'" border="0" width="0" height="0" style="display:none;" alt="site statistics"></span><\/a>');
      }
      else {
         document.write(qry+'" border="0" alt="site statistics"></span><\/a>');
      }

   }

}



function pausecomp(wtsms){
  wtsd = new Date();
  while (1){
    wtsmill=new Date();
    wtsdiff = wtsmill-wtsd;
    if(wtsdiff > wtsms){
      break;
    }
  }
}




