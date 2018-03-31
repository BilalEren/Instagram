
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Instagram</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script type="text/javascript">
    
        $(document).ready(function(){
            var client_id = '';//instagram developer console da yapılan uygulama idsi.
            var uri = '';//instagram developer console da verdiginiz redirect uri localde de calısır.       
            
            if(!window.location.hash.match('access')){
                $('#token').html('<a href="https://www.instagram.com/oauth/authorize/?client_id='+client_id+'&redirect_uri='+uri+'&response_type=token&scope=public_content">Token</a>');
            }
            
            var has = window.location.hash.slice(1);//urlden gelen access_token hasini alıyoruz.
            var token = has.replace(/access_token=/gi,'');//hasin basında olan access_token= adlı kısmı degistiriyoruz
            
            
            //Instagram verisini alarak sayfamızda gösterme.
            $.ajax({
                url: 'https://api.instagram.com/v1/users/self/media/recent/?access_token='+token+'&count=30',
                type: 'GET',
                dataType: 'json',
                cache: false,
                success: function(data){
                    var veri = data.data,
                        html = '';
                    
                    function caption(str){
                        var metin = str.split(' '),
                            yeniMetin = '';
                        
                        for(var i = 0; i < metin.length; i++){
                            var kelime;
                            
                            if(metin[i][0] == '#'){
                                var a = '<a href="https://www.instagram.com/explore/tags/'+metin[i].replace('#','').toLowerCase()+'" class="external" target="_blank">' + metin[i] + '</a>';
								kelime = a;
                            } else if(metin[i][0] == '@'){
                                var a = '<a href="https://www.instagram.com/'+metin[i].replace('@','').toLowerCase()+'" class="external" target="_blank">' + metin[i] + '</a>';
								kelime = a;
                            } else {
                                kelime = metin[i];
                            }
                            
                            yeniMetin += kelime + ' ';
                        }
                        
                        return yeniMetin;
                    }
                    
                    console.log(data);
                    
                    for(var i = 0; i < veri.length; i++){
                        
                        if (veri[i].caption != null) {
                            if(veri[i].caption.text != null) { 
                                var baslik = veri[i].caption.text;
                            } 
                        } else { 
                            var baslik = "";
                        }
                        
                        html += '<div>';
                        html += '<img src="'+veri[i].images.standard_resolution.url+'" width="400px" height="400px">';
                        html += '<p>'+caption(baslik)+'</p>';
                        html += '</div>';
                    }
                    
                    $('#data').append(html);
                }
            });
            
            $('#token_val').val(token);//aldıgımız tokeni hidden türüne sahip olan input eleamanın değerine yazdırıyoruz.
                        
            //Aldığımız access_token adlı has parametresini post ile php dosyasına gönderiyoruz.
            $('button').click(function(){
               $.ajax({
                    url: 'token.php',
                    type: 'POST',
                    data: $('form').serialize(),
                    success: function(data){
                        alert($.trim($(data).filter('span').text()));
                    }
                });
                
                return false;
            });
            
            $('button').trigger('click');//sayfa yüklenir yüklenme butona tıklama işlemi gerçekleşti.
        });
        
    </script>
    
</head>
<body>
    <div id="token"></div>
    <div id="data"></div>
    
    <form method="post" style="display: none;">
        <input type="hidden" name="token" id="token_val">
        <button type="submit">Gonder</button>
    </form>
</body>
</html>
