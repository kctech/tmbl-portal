<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title>@yield('title', 'Adviser Portal Email')</title>

    <style type="text/css">
        /* Outlines the grids, remove when sending
        table td { border: 1px solid cyan; }*/

        /* CLIENT-SPECIFIC STYLES */
        body, table, td, a{-webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;} /* Prevent WebKit and Windows mobile changing default text sizes */
        table, td{mso-table-lspace: 0pt; mso-table-rspace: 0pt;} /* Remove spacing between tables in Outlook 2007 and up */
        img{-ms-interpolation-mode: bicubic;} /* Allow smoother rendering of resized image in Internet Explorer */

        /* RESET STYLES */
        img{border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none;}
        table{border-collapse: collapse !important;}
        body{height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important;background-color:#f2f2f2;}
        a{color:#2e96d8;}

        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
            font-size: inherit !important;
            font-family: inherit !important;
            font-weight: inherit !important;
            line-height: inherit !important;
        }

        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] { margin: 0 !important; }

        .timerImg {width:400px; height:80px;}

        /* MEDIA QUERIES */
        @media all and (max-width:639px){
            .wrapper{ width:320px!important; padding: 0 !important; }
            .container{ width:300px!important;  padding: 0 !important; }
            .mobile{ width:300px!important; display:block!important; padding: 0 !important; }
            .img{ width:100% !important; height:auto !important; }
            .mobileOff, *[class="mobileOff"] { width: 0px !important; display: none !important; }
            .mobileOn, *[class*="mobileOn"] { display: block !important; max-height:none !important; }
            .timerImg {width:100%; height:auto;}
        }

        @stack('css')
    </style>
</head>

<body style="margin:0; padding:0; background-color:#F2F2F2;">

    <!-- HIDDEN PREHEADER TEXT -->
    <div style="display: none; font-size: 1px; color: #fefefe; line-height: 1px; font-family: Helvetica, Arial, sans-serif; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden;">
        @yield('preheader')
    </div>

    <span style="display: block; width: 640px !important; max-width: 640px; height: 1px" class="mobileOff"></span>

    <center>
        <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#F2F2F2">
            <tr>
                <td align="center" valign="top">

                    <!--HEADER-->
                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper">
                        <tr>
                            <td height="15" style="font-size:15px; line-height:15px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">

                                <table align="center" width="100%">

                                </table>

                                <!--LOGO-->

                                <table width="640" cellpadding="0" cellspacing="0" border="0" class="mobileOff" bgcolor="#F2F2F2">
                                    <tr>
                                        <td align="center">

                                            <table width="640" cellpadding="0" cellspacing="0" border="0" class="container">
                                                <tr>
                                                    <td style="text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 10px;" valign="middle">
                                                        <a href="@yield('link','https://themortgagebroker.co.uk')" target="_blank"><img src="@yield('logo', 'https://tmblportal.co.uk/img/TMBL/email/tmbl_logo.png')" alt="Logo" style="margin:0; padding:0; border:none; display:inline-block; height: 143px; width: auto;" border="0" class="imgClass"></a>
                                                    </td>
                                                </tr>
                                            </table>

                                        </td>
                                    </tr>
                                </table>

                                <!--[if !mso]><!-- -->
                                <div class="mobileOn" style="font-size: 0; max-height: 0; overflow: hidden; display: none">
                                    <table width="320" cellspacing="0" cellpadding="0" border="0" class="wrapper" align="center">
                                        <tr>
                                            <td style="text-decoration: none; text-align: center; font-family: 'Verdana', sans-serif;font-size: 10px;" valign="middle">
                                                <a href="@yield('link','https://themortgagebroker.co.uk')" target="_blank"><img src="@yield('logo', 'https://tmblportal.co.uk/img/TMBL/email/tmbl_logo.png')" alt="Logo" style="margin:0; padding:0; border:none; display:inline-block; height: 143px; width: auto;" border="0" class="imgClass"></a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!--<![endif]-->

                                <!--END LOGO-->

                            </td>
                        </tr>
                        <tr>
                            <td height="15" style="font-size:15px; line-height:15px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="3" style="font-size:3px; line-height:3px;" bgcolor="#2e96d8">&nbsp;</td>
                        </tr>
                    </table>
                    <!--END HEADER-->

                    <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                        <tr>
                            <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center" valign="top">

                                <!-- PRIMARY -->

                                <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                    <tr>
                                        <td align="left" valign="top" style="font-size: 12px; line-height: 25px; font-family: Helvetica, Arial, sans-serif; color: #222222;">

                                            @yield('content')

                                        </td>

                                    </tr>
                                </table>

                                <!-- END PRIMARY -->

                            </td>
                        </tr>
                        <tr>
                            <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                        </tr>
                    </table>

                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 40px 0px;">
                    <!-- FOOTER -->
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" style="max-width: 600px;" class="responsive-table">
                        <tr>
                            <td align="center" style="font-size: 10px; line-height: 18px; font-family: Helvetica, Arial, sans-serif; color:#666666;">
                                @hasSection('footer')
                                    @yield('footer')
                                @else
                                    Trouble displaying this email? <a href="@yield('link','https://themortgagebroker.co.uk')" target="_blank" style="color: #666666; text-decoration: none;">View it in your browser</a>
                                @endif 
                            </td>
                        </tr>
                    </table>
                    <!-- FOOTER -->
                </td>
            </tr>
        </table>
    </center>
</body>

</html>
