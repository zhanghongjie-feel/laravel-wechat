<?php
$num=2;
$sum=10;
switch(1){
    case 1;
        $sum=$sum+10;
    case 2;
        $sum=$sum+10;
    case 3;
        $sum=$sum+10;
   default;
        $sum=$sum+10;
}
echo $sum;

//break直接终止switch执行
        //如果不写break,他会根据switch里面的条件到符合条件的case里一直执行到最后