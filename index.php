<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Instagram</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <script type="text/javascript">
    
        $(document).ready(function(){
            var client_id = 'ff91d1509c6546d7b65ca696ee4dabb3';//instagram developer console da yapılan uygulama idsi.
            var uri = 'http://localhost/projeler/ins/index.php';//instagram developer console da verdiginiz redirect uri localde de calısır.       
            
            if(!window.location.hash.match('access')){
                $('#token').html('<a href="https://www.instagram.com/oauth/authorize/?client_id='+client_id+'&redirect_uri='+uri+'&response_type=token&scope=public_content">Token</a>');
            }
            
            var has = window.location.hash.slice(1);//urlden gelen access_token hasini alıyoruz.
            var token = has.replace(/access_token=/gi,'');//hasin basında olan access_token= adlı kısmı degistiriyoruz
            
            
            //Profil bilgilerine erişme
            $.ajax({
                url: 'https://api.instagram.com/v1/users/self/?access_token='+token,
                dataType: 'json',
                type: 'GET',
                success: function(data){
                    var html = '';
                    console.log(data);
                    html += '<p><img src="'+data.data.profile_picture+'" style="border-radius: 50%" width="50px" height="50px"></p>';
                    html += '<p>İsim: '+data.data.full_name+'</p>';
                    html += '<p>Kullanıcı Adı: '+data.data.username+'</p>';
                    if(data.data.website != ""){
                        html += '<p> Website: '+'<a target="_blank" href="'+data.data.website+'">'+data.data.website+'</a>'+'</p>'
                    } else {
                        html += '<p>Website: Websitesi yok.</p>';
                    }
                    if(data.data.bio != ""){
                        html += '<p>Biografi: '+data.data.bio+'</p>';
                    } else {
                        html += '<p>Biografi: Biografi yok.</p>';
                    }
                    if(data.data.counts.media != ""){
                        html += '<p>Medya: '+data.data.counts.media+'</p>';
                    } else {
                        html += '<p>Medya: Paylaşılan fotoğraf veya video bulunamadı.</p>';
                    }
                    html += '<p>Takipçi: '+data.data.counts.followed_by+'</p>';
                    html += '<p>Taki Edilen: '+data.data.counts.follows+'</p>';
                    
                    $('#bilgi').append(html);
                }
            });
            
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
                        
                        var tarih = new Date(veri[i].created_time * 1000),
                            gun = tarih.getDate(),
                            ay = tarih.getMonth(),
                            yil = tarih.getFullYear();
                        
                        var aylar = ["Ocak","Şubat","Mart","Nisan","Mayıs","Haziran","Temmuz","Ağustos","Eylül","Ekim","Kasım","Aralık"];
                        var gunler = ["Pazartesi","Salı","Çarşamba","Perşembe","Cuma","Cumartesi","Pazar"];
            
                        var tarihstr =  gun + ' ' + aylar[ay] + ' ' + yil + ' ' + gunler[tarih.getDay()];
                        
                        html += '<div>';
                        if(veri[i].type == "image" || !veri[i].type == "video"){
                            html += '<img src="'+veri[i].images.standard_resolution.url+'" width="400px" height="400px">';
                        } else if(veri[i].type == "video" || !veri[i].type == "image"){
                            html += '<video width="400px" height="400px" controls poster="'+veri[i].images.standard_resolution.url+'">';
                            html += '<source src="'+veri[i].videos.standard_resolution.url+'" type="video/mp4">';
                            html += '</video>';
                        }
                        html += '<a href="'+veri[i].link+'" target="_blank"><p>'+caption(baslik)+'</p></a>';
                        html += '<p><span>Beğeni: '+veri[i].likes.count+ ' <span>Yorum:'+veri[i].comments.count+'</span></p>';
                        html += '<p>'+tarihstr+'</p>';
                        html += '</div><hr/>';
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
            
            $('button').trigger('click');//sayfa yüklenir yüklenmez butona tıklama işlemi gerçekleşti.
        });
        
    </script>
    
</head>
<body>
    <div id="token"></div>
    <div id="bilgi"></div>
    <div id="data"></div>
    
    <form method="post" style="display: none;">
        <input type="hidden" name="token" id="token_val">
        <button type="submit">Gonder</button>
    </form>
</body>
</html>
