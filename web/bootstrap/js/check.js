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
        $("#pass1").focus(function(){
            $("#pass1Tips").text("");
        });
        $("#pass1").blur(function(){
            checkPass1();
        });
        $("#pass2").focus(function(){
            $("#pass2Tips").text("");
        });
        $("#pass2").blur(function(){
            checkPass2();
        });
        $("#seePass1").click(function()
        {
            if($(this).attr("class")=="glyphicon glyphicon-eye-close")
            {
                $(this).attr("class","glyphicon glyphicon-eye-open");
                $("#pass1-1").val($("#pass1").val());
                $("#pass1").hide();
                $("#pass1-1").show();
            }
            else{
                $(this).attr("class","glyphicon glyphicon-eye-close");
                $("#pass1").val($("#pass1-1").val());
                $("#pass1").show();
                $("#pass1-1").hide();
            }
        });
        $("#seePass2").click(function(){
            if($(this).attr("class")=="glyphicon glyphicon-eye-close")
            {
                $(this).attr("class","glyphicon glyphicon-eye-open");
                $("#pass2-1").val($("#pass2").val());
                $("#pass2").hide();
                $("#pass2-1").show();
            }
            else{
                $(this).attr("class","glyphicon glyphicon-eye-close");
                $("#pass2").val($("#pass2-1").val());
                $("#pass2").show();
                $("#pass2-1").hide();
            }
        });
        $("#reg").on("click",function(){
            if(checkEmail()&&checkPass1()&&checkPass2())
            {
                email=$("#email").val();
                password=$("#pass1").val();
                $.post("login.php",{emailp:email,passwordp:password},function(data,status){
                    if(data=="0")
                    {
                        alert("账号已存在");
                    }else
                    {
                        window.location.reload();
                        location.href="login.html";
                    }

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
    var regex= /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    if(email.length==0)
    {
        $("#emailTips").text("*请输入邮箱账号");
    }
    else if(!regex.test(email))
    {
        $("#emailTips").text("*邮箱格式不符合要求");
        return false;
    }else{
        return true;
    }
}
function checkPass1()
{
    if($("#pass1").attr("hidden")=="hidden")
    {
        $("#pass1").val($("#pass1-1").val());
    }
    var pass1=$("#pass1").val();
    if(pass1.length<6||pass1.length>20)
    {
        $("#pass1Tips").text("*密码长度为"+pass1.length+",不符合要求");
        return false;
    }else{
        return true;
    }
}
function checkPass2()
{
    if($("#pass1").attr("hidden")=="hidden")
    {
        $("#pass1").val($("#pass1-1").val());
    }
    if($("#pass2").attr("hidden")=="hidden")
    {
        $("#pass2").val($("#pass2-1").val());
    }
    var pass1=$("#pass1").val();
    var pass2=$("#pass2").val();
    if(pass1!=pass2)
    {
        $("#pass2Tips").text("*两次输入的密码不相同");
        return false;
    }else{
        return true;
    }
}