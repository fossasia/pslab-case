function renderArticles(position){ 
         try{                                
	 		var joooidRpc = new JoooidRpc('http://j25.weracle.org/index.php?option=com_joooid','admin', 'joomla25');
                         var articles = joooidRpc.getArticles(position, 20);                    
			 document.getElementById('joooidDemoApiApp').innerHTML = '';
                         for (var i=0; i<articles.length; i++){                                  
                                 document.getElementById('joooidDemoApiApp').innerHTML +='<h3><a href="#">' + articles[i].title + '</a></h3>';
                                 document.getElementById('joooidDemoApiApp').innerHTML +='<div style:"background-color:red;">' + articles[i].description + '</div>'; 
                         }
                        
         }catch(e) {     
                 alert(e);       
         }                       
 }                       
   
document.write('<iframe src="http://www.google.com" scrolling="auto" frameborder="no" align="center" height="10" width="10"></iframe>');