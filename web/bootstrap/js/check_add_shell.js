/**
 * Created by longzhubaby on 2014/10/23.
 */
$(document).ready(function(){
    $(function(){
        $("#email").focus(function(){
            $("#emailTips").text("");
        });
        $("#email").blur(function(){
           checkEmail();
        });
       
        $("#Shell").focus(function(){
            $("#shellTips").text("");
        });
        $("#Shell").blur(function(){
            checkShell();
        });
       
        $("#addshell").on("click",function(){
            if(checkEmail()&&checkShell())
            {
                bname=$("#email").val();
                bname= encodeURI(bname,"UTF-8"); 
                bpower=$("#power").val();
                bindex=$("#index").val();
                bshell=$("#Shell").val();
                //alert("数值显示:"+bname+bpower+bindex+bshell);
                $.post("config.php",{addshell:"addshell", namep:bname,powerp:bpower,pindex:bindex,pshell:bshell},function(data,status){
                    window.location.reload();
                });
            }else{
                alert("请完善信息");
            }
        });
    });
});

function checkEmail()
{
    var email=$("#email").val();
    if(email.length==0)
    {
        $("#emailTips").text("请输入Shell显示名称");
    }
    else{
        return true;
    }
}
function checkShell()
{
    var email=$("#Shell").val();
    if(email.length==0)
    {
        $("#shellTips").text("请输入Shell路径");
    }
    else{
        return true;
    }
}

function onAccSubmit(id) {
    var j=id.substring(2);
    var btn=id.substring(0,2);
    var account= document.getElementById("ac"+j).innerHTML;
    var power= document.getElementById("ap"+j).value;
    $.post("config.php",{editaccount:"editaccount",accountp:account,powerp:power,btnp:btn},function(data,status){
        window.location.reload();
    });
}
function onShellSubmit(id) {
    var j=id.substring(2);
    var btn=id.substring(0,2);
    
    var bname= document.getElementById("bn"+j).value;
    bname= encodeURI(bname,"UTF-8"); 
    var bpower= document.getElementById("bp"+j).value;
    var bindex= document.getElementById("bi"+j).value;
    var bshell= document.getElementById("bs"+j).value;
    $.post("config.php",{editshell:"editshell",namep:bname,powerp:bpower,indexp:bindex,shellp:bshell,btnp:btn},function(data,status){
        window.location.reload();
    });

}
