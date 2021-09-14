<?php
    header("Content-type: text/css");
    $color = strip_tags(urldecode($_GET['color']));
    $customColor = 'rgb('.$color.')';
    $customColorHover = 'rgba('.$color.', 0.4)';
?>

:root{
    --custom-color: <?=$customColor?>;
    --custom-color-hover: <?=$customColorHover?>;
}

.skin-blue-light .main-header .navbar {
    background-color: var(--custom-color);
}

.skin-blue-light .main-header .logo {
    background-color: var(--custom-color);
    color: #fff;
    border-bottom: 0 solid transparent;
}

.skin-blue-light .main-header .logo:hover {
    background-color: var(--custom-color);
    color: #fff;
    border-bottom: 0 solid transparent;
}

.bg-blue, .label-primary, .btn-primary {
    background-color: var(--custom-color) !important;
}
.panel-heading{
    background-color:rgb(232, 233, 235) !important;
    height: 50px !important;
    border-top: none;
    border-right:none;
    border-left:none;
    border-bottom:1px #black solid;
}
.panel-body{
    padding-left:20px !important;
    border-top: none;
    border-right:none;
    border-left:none;
    border-bottom:1px #black solid;
}
.panel-default{
    border-top: none;
    border-right:none;
    border-left:none;
    border-bottom:1px #black solid;
}
.panel-title{
    border-top: none;
    border-right:none;
    border-left:none;
    border-bottom:1px #black solid;
}
.accordion-title{
    margin-left: auto;
    margin-right: auto;
    display: block;
   height: 40px;
}
.box-body{
    height: auto !important;
}

.api{
    position: relative;
    padding-right: 15px;
    padding-left: 45px;
    margin-top: -40px;
}
.card-tipe-title{
    color:#C1C7CC;
}
.card-tipe-subtitle{
    font-weight: 700;
}
.copy-icon{
    margin-left: 10px;
}
.copy-icon:hover{
    cursor: pointer;
} 
.api-section{
    margin-left:20px;
}
 .banks-section{
     margin-left: 10px;
    margin-top: 25px;
}
.btn-bank{
    margin-top:5px;
    margin-left: 130px;
}

.btn-delete{
    background-color: #FACCCC;
}
.btn-edit{
    background-color: #D5F5E2;
    margin-right: 10px;
}
.form-copy-text{
    border:none;   
}
.none{
    display:none;
}
.btn-password{
    background-color: transparent;
}
.mt{
    margin-top: 20px;
}
.mr{
    margin-right:470px;
}
.btn-blue{
    background-color: var(--custom-color);
    color: #fff;
    border-radius: 4px;
   
  }
@media(min-width:360px) {
    .accordion-title{
        margin-left: auto;
        margin-right: auto;
        display: block;
        height: 40px;
     }
}
@media(min-width:576px){
    .accordion-title{
        margin-left: auto;
        margin-right: auto;
        display: block;
        height: 40px;
     }
} 

.card-payment{
    margin-left:15px;
    border-radius: 8px;
    border: none;
  }
  .payment-card{
    padding: 1rem;
    text-align: center !important;
  }
  
  .card-payment > .payment-card{
    width: 150px; 
    height: 73px;
    box-shadow: 0px 4px 24px 0px rgba(16,16,16,0.08) !important;
    -webkit-box-shadow: 0px 4px 24px 0px rgba(16,16,16,0.08) !important;
    -moz-box-shadow: 0px 4px 24px 0px rgba(16,16,16,0.08) !important;
    border: none;
    border-radius: 8px;
  }
  
  .card-payment > .payment-card:hover{
    width: 150px; 
    height: 73px;
    box-shadow: 0px 8px 24px 0px var(--custom-color) !important;
    -webkit-box-shadow: 0px 8px 24px 0px var(--custom-color-hover) !important;
    -moz-box-shadow: 0px 8px 24px 0px var(--custom-color-hover) !important;
    border: solid 1px var(--custom-color);
    border-radius: 8px;
  }
  
  .subjudul{
    color: #394654;
    font-size: 18px;
    font-weight: 600;
  }

  .label-jam{
    position: absolute;
    font-size: 10px;
    border-radius: 15px 8px 0px 15px;
    width: 50px;
    right: 5px;
    top: 0px;
    text-align: center;
    color: #FFF;
    background-color: var(--custom-color);
    padding: 0.5rem 0.5rem 0.5rem 0.5rem;
  }
  @media(min-width:360px) and (max-width :576px){
    .label-jam{
        position: absolute;
        font-size: 10px;
        border-radius: 15px 8px 0px 15px;
        width: 50px;
        right: 220px;
        top: 0px;
        text-align: center;
        color: #FFF;
        background-color: var(--custom-color);
        padding: 0.5rem 0.5rem 0.5rem 0.5rem;
      }
    } 

  .deposit__title{
    font-size: 16px;
    color: #A6AABB;
    margin-left: 1.8rem;
    font-family: auto;
    margin-top:1rem;
    margin-bottom: 0.5rem;
  }
  .img-bank{
   margin-top: 10px;
  }