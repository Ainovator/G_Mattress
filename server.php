<?php

// Реализованные расчёты:
// Развёртка деталей
// Длина рулона
// Пена 
// Ткань
// Молния
// Работа
// Холкон
// Пуговицы





// Включаем отображение ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$response = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        //Массив с ценами на материалы, заменить на данные из админки
        $Cost_Foam = [
            'LL5020' => 722,
            'HR3535' => 722, 
            'ST2236' => 572,
            'ST3040' => 572,
            'EL4065' => 648,
            'HR3030' => 682,
            'HR3020' => 682,
            'VE3508' => 644,
            'NP2300' => 410,
        ];
        $textile_width = 1400;
        $scale_up = 20;

        //Цена допов заменить на данные из админки
        $holcon_cost = 128;
        $zipper_cost = 10;
        $velcro_cost = 56;
        $Pug_Cost = 5;
        $Production_MarkUp = 1.2;
        $Wholesale_MarkUp = 1.6;
        $Retail_Markup = 1.3;


        // Данные ручных полей ввода 
        $width = isset($_POST['width']) ? floatval($_POST['width']) : 0;
        $length = isset($_POST['length']) ? floatval($_POST['length']) : 0;
        $bold = isset($_POST['bold']) ? floatval($_POST['bold']) : 0;
        $quantity = isset($_POST['quantity']) ? floatval($_POST['quantity']) : 1;
        $WorkTime = isset($_POST['work-time']) ? floatval($_POST['work-time']) : 0;
        $TextileCost = isset($_POST['textile-cost']) ? floatval($_POST['textile-cost']) : 0;

        //
        $pugOnLength= isset($_POST['pugOnLength']) ? floatval($_POST['pugOnLength']) : 0;
        $pugOnWidth= isset($_POST['pugOnWidth']) ? floatval($_POST['pugOnWidth']) : 0;
        $doublePug = isset($_POST['doublePug']) ? intval($_POST['doublePug']) : 0;


        // Данные карточек 
        $no_bort = isset($_POST['no_bort']) ? intval($_POST['no_bort']) : 0;
        $with_bort = isset($_POST['with_bort']) ? intval($_POST['with_bort']) : 0;
        $no_cant = isset($_POST['no_cant']) ? intval($_POST['no_cant']) : 0;
        $with_cant = isset($_POST['with_cant']) ? intval($_POST['with_cant']) : 0;
        $zipper_side = isset($_POST['zipper_side']) ? intval($_POST['zipper_side']) : 0;
        $zipper_bottom = isset($_POST['zipper_bottom']) ? intval($_POST['zipper_bottom']) : 0;
        $uno_stitch = isset($_POST['uno_stitch']) ? intval($_POST['uno_stitch']) : 0;
        $double_stitch = isset($_POST['double_stitch']) ? intval($_POST['double_stitch']) : 0;
        $with_pug = isset($_POST['with_pug']) ? intval($_POST['with_pug']) : 0;
        $pik = isset($_POST['pik']) ? intval($_POST['pik']) : 0;

        // Данные о слоях
        $material_first_layer = isset($_POST['material_first_layer']) ? $_POST['material_first_layer'] : '';
        $bold_first_layer = isset($_POST['bold_first_layer']) ? intval($_POST['bold_first_layer']) : 0;
        $density_first_layer = isset($_POST['material_first_layer']) ? intval(substr($_POST['material_first_layer'], 2, 2)) : 0;

        $material_second_layer = isset($_POST['material_second_layer']) ? $_POST['material_second_layer'] : '';
        $bold_second_layer = isset($_POST['bold_second_layer']) ? intval($_POST['bold_second_layer']) : 0;
        $density_second_layer = isset($_POST['material_second_layer']) ? intval(substr($_POST['material_second_layer'], 2, 2)) : 0;

        $material_third_layer = isset($_POST['material_third_layer']) ? $_POST['material_third_layer'] : '';
        $bold_third_layer = isset($_POST['bold_third_layer']) ? intval($_POST['bold_third_layer']) : 0;
        $density_third_layer = isset($_POST['material_third_layer']) ? intval(substr($_POST['material_third_layer'], 2, 2)) : 0;
        
        // Стоимость материала слоя
        $cost_first_layer = isset($Cost_Foam[$material_first_layer]) ? floatval($Cost_Foam[$material_first_layer]) : 0;
        $cost_second_layer = isset($Cost_Foam[$material_second_layer]) ? floatval($Cost_Foam[$material_second_layer]) : 0;
        $cost_third_layer = isset($Cost_Foam[$material_third_layer]) ? floatval($Cost_Foam[$material_third_layer]) : 0;

        // Выбранные карточки выреза
        $selected_cards = isset($_POST['selected_cards']) ? json_decode($_POST['selected_cards'], true) : [];
          
         

  

        // Развёртка деталей матраса
        function countDetails($Input_Mattress_Width, $Input_Textile_Width, $Input_Bort_Value, $Input_Mattress_Length, $Input_Mattress_Bold, $Input_Mattress_Amount, $ScaleUp) {
            $details = [];
            
            if ($Input_Mattress_Width < $Input_Textile_Width && $Input_Bort_Value === 1) {
                for ($i = 0; $i < $Input_Mattress_Amount * 2; $i++) {
                    $details[] = [$Input_Mattress_Width + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                    $details[] = [$Input_Mattress_Width + $ScaleUp * 2, $Input_Mattress_Bold + $ScaleUp * 2];
                    $details[] = [$Input_Mattress_Bold + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                }
            } elseif ($Input_Mattress_Width > $Input_Textile_Width && $Input_Bort_Value === 1) {
                for ($i = 0; $i < $Input_Mattress_Amount * 2; $i++) {
                    $details[] = [($Input_Mattress_Width / 2) + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                    $details[] = [($Input_Mattress_Width / 2) + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                    $details[] = [($Input_Mattress_Width / 2) + $ScaleUp * 2, $Input_Mattress_Bold + $ScaleUp * 2];
                    $details[] = [$Input_Mattress_Bold + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                }
            } elseif ($Input_Mattress_Width < $Input_Textile_Width && $Input_Bort_Value === 0) {
                for ($i = 0; $i < $Input_Mattress_Amount; $i++) {
                    $details[] = [($Input_Mattress_Width + $Input_Mattress_Bold * 2) + $ScaleUp * 2, ($Input_Mattress_Length + $Input_Mattress_Bold * 2) + $ScaleUp * 2];
                    $details[] = [$Input_Mattress_Width + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                }
            } elseif ($Input_Mattress_Width > $Input_Textile_Width && $Input_Bort_Value == 0) {
                for ($i = 0; $i < $Input_Mattress_Amount; $i++) {
                    $details[] = [($Input_Mattress_Width / 2 + $Input_Mattress_Bold) + $ScaleUp * 2, ($Input_Mattress_Length + $Input_Mattress_Bold * 2) + $ScaleUp * 2];
                    $details[] = [($Input_Mattress_Width / 2 + $Input_Mattress_Bold) + $ScaleUp * 2, ($Input_Mattress_Length + $Input_Mattress_Bold * 2) + $ScaleUp * 2];
                    $details[] = [$Input_Mattress_Width / 2 + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                    $details[] = [$Input_Mattress_Width / 2 + $ScaleUp * 2, $Input_Mattress_Length + $ScaleUp * 2];
                }
            }
            
            return $details;
        }

        // Поиск минимальной длины отреза ткани
        function bestFit($width, $parts) {
            $minRollLength = INF;
            $bestArrangement = [];

            class Node {
                public $x;
                public $y;
                public $width;
                public $height;
                public $used = false;
                public $right = null;
                public $down = null;

                public function __construct($x, $y, $width, $height) {
                    $this->x = $x;
                    $this->y = $y;
                    $this->width = $width;
                    $this->height = $height;
                }

                public function insert($partWidth, $partHeight) {
                    if ($this->used) {
                        $rightResult = $this->right ? $this->right->insert($partWidth, $partHeight) : null;
                        $downResult = $this->down ? $this->down->insert($partWidth, $partHeight) : null;
                        return $rightResult ?: $downResult;
                    } else if ($partWidth <= $this->width && $partHeight <= $this->height) {
                        $this->used = true;
                        $this->right = new Node($this->x + $partWidth, $this->y, $this->width - $partWidth, $partHeight);
                        $this->down = new Node($this->x, $this->y + $partHeight, $this->width, $this->height - $partHeight);
                        return $this;
                    } else {
                        return null;
                    }
                }
            }

            function fit($parts, $width) {
                $root = new Node(0, 0, $width, INF);
                $maxY = 0;

                usort($parts, function($a, $b) {
                    return $b[1] - $a[1];
                });

                foreach ($parts as &$part) {
                    $node = $root->insert($part[0], $part[1]);
                    if ($node) {
                        $part['x'] = $node->x;
                        $part['y'] = $node->y;
                        $maxY = max($maxY, $node->y + $part[1]);
                    } else {
                        return INF;
                    }
                }

                return $maxY;
            }

            $rollLength = fit($parts, $width);

            if ($rollLength < $minRollLength) {
                $minRollLength = $rollLength;
                $bestArrangement = array_map(function($part) {
                    return [
                        'x' => isset($part['x']) ? $part['x'] : null,  // Проверка существования ключа 'x'
                        'y' => isset($part['y']) ? $part['y'] : null,  // Проверка существования ключа 'y'
                        'width' => $part[0],
                        'height' => $part[1]
                    ];
                }, $parts);
            }

            return [
                'details' => $bestArrangement,
                'rollLength' => $minRollLength
            ];
        };

        // Расчёт стоимости отреза ткани
        function calculateTextileCost($TextileCost, $rollLength) {
            // Рассчитываем стоимость ткани
            $Full_Textile_Cost = round($TextileCost * ($rollLength / 1000) * 1000)/1000;
            
            return $Full_Textile_Cost;
        }

         // Расчёт пены
        function calculateFoamCost($Input_Mattress_Length, $Input_Mattress_Width, $Bold_First_Layer, $Material_First_Layer, $Cost_First_Layer, $Bold_Second_Layer, $Material_Second_Layer, $Cost_Second_Layer, $Bold_Third_Layer, $Material_Third_Layer, $Cost_Third_Layer, $Input_Mattress_Bold) {
    
            // Рассчитываем стоимость первого слоя
            $Full_Cost_First_Layer = ($Input_Mattress_Length / 1000) * ($Input_Mattress_Width / 1000) * ($Bold_First_Layer / 1000) * $Material_First_Layer * $Cost_First_Layer;
        
            // Рассчитываем стоимость второго слоя
            $Full_Cost_Second_Layer = ($Input_Mattress_Length / 1000) * ($Input_Mattress_Width / 1000) * ($Bold_Second_Layer / 1000) * $Material_Second_Layer * $Cost_Second_Layer;
        
            // Рассчитываем стоимость третьего слоя
            $Full_Cost_Third_Layer = ($Input_Mattress_Length / 1000) * ($Input_Mattress_Width / 1000) * ($Bold_Third_Layer / 1000) * $Material_Third_Layer * $Cost_Third_Layer;
        
            // Рассчитываем стоимость отбортовки
            $Otbortovka = 0;
            if ($Input_Mattress_Bold >= 100) {
                $Otbortovka = (($Input_Mattress_Length / 1000) * ($Input_Mattress_Bold / 1000) * 0.05 * 25 * 2 * 478) +
                              (($Input_Mattress_Width / 1000) * ($Input_Mattress_Bold / 1000) * 0.05 * 25 * 2 * 478);
        
                // Повторная проверка
                if ($Input_Mattress_Bold >= 200) {
                    $Otbortovka = (($Input_Mattress_Length / 1000) * ($Input_Mattress_Bold / 1000) * 0.05 * 25 * 2 * 476) +
                                  (($Input_Mattress_Width / 1000) * ($Input_Mattress_Bold / 1000) * 0.05 * 25 * 2 * 476);
                } else {
                    $Otbortovka = 0;
                }
            }
        
            // Общая стоимость пены для всех слоёв + стоимость отбортовки
            $Full_Cost_Foam = round(($Full_Cost_First_Layer + $Full_Cost_Second_Layer + $Full_Cost_Third_Layer + $Otbortovka) * 1000) / 1000;
        
            return $Full_Cost_Foam;
        }

        // Расчёт работ (заменить на excel)
        function calculateWorkCost($Input_Full_Work) {
            // Рассчитываем стоимость работы
            $Full_Work_Cost = round((($Input_Full_Work * 9) / 1000) * 1000);
            
            return $Full_Work_Cost;
        }

        // Расчёт молнии
        function calculateZipperCost($Input_Mattress_Length, $Input_Mattress_Width, $zipper_cost) {
            // Рассчитываем стоимость молнии
            $zipperCost = ($Input_Mattress_Length / 1000 + $Input_Mattress_Width / 1000) * $zipper_cost;
            
            return $zipperCost;
        }

        //Расчёт холкона 
        function calculateHolcon($Input_Mattress_Length, $Input_Mattress_Width, $holcon_cost) {
            $full_holcon_cost = round(($Input_Mattress_Length/1000) * ($Input_Mattress_Width/1000) * $holcon_cost)/1000*1000;

            return $full_holcon_cost;
        }

        //Расчёт пуговиц
       // Расчёт пуговиц
       function calculatePug($with_pug, $pugOnLength, $pugOnWidth, $Pug_Cost, $doublePug) {
        if ($with_pug === 1) {
            if ($doublePug === 1) {
                $Full_Pug_Cost = ($pugOnLength * $pugOnWidth * $Pug_Cost) * 2; // Умножаем на 2, если выбрано с двух сторон
            } else {
                $Full_Pug_Cost = $pugOnLength * $pugOnWidth * $Pug_Cost;
            }
        } else {
            $Full_Pug_Cost = 0;
        }
    
        return $Full_Pug_Cost;
    }
    




        //Расчёт себестоимости
        function totalCost($Input_Mattress_Amount ,$Full_Textile_Cost, $Full_Cost_Foam, $Work_Cost, $full_holcon_cost, $full_zipper_cost, $Full_Pug_Cost){

            $totalCost = $Full_Textile_Cost + ($Full_Cost_Foam + $Work_Cost + $full_holcon_cost + $full_zipper_cost + $Full_Pug_Cost) * $Input_Mattress_Amount;

            return $totalCost;
        };

        function finalCost($totalCost, $Production_MarkUp, $Wholesale_MarkUp, $Retail_Markup) {

            $finalCost = round(($totalCost * $Production_MarkUp * $Wholesale_MarkUp * $Retail_Markup)/1)*1;

            return $finalCost;
        };

        //Стоимость ткани => FullTextileCost
        $details = countDetails($width, $textile_width, $with_bort, $length, $bold, $quantity, $scale_up);
        $resultBestFit = bestFit($textile_width, $details);
        $FullTextileCost = calculateTextileCost($TextileCost, $resultBestFit['rollLength']);

        //Стоимость пены
        $Full_Cost_Foam = calculateFoamCost($length,$width,$bold_first_layer,$density_first_layer,$cost_first_layer,$bold_second_layer,$density_second_layer,$cost_second_layer,$bold_third_layer,$density_third_layer,$cost_third_layer,$quantity,$bold);

        //Стоимость работ (заменить на Excel)
        $Work_Cost = calculateWorkCost($WorkTime, $quantity);

        //Стоимость доп. материалов
        $full_holcon_cost = calculateHolcon($length, $width, $holcon_cost);
        $full_zipper_cost = calculateZipperCost($length, $width, $zipper_cost);
        $Full_Pug_Cost = calculatePug($doublePug, $with_pug, $pugOnLength, $pugOnWidth, $Pug_Cost);
        error_log("doublePug: " . $doublePug);
        error_log("pugOnLength: " . $pugOnLength);
        error_log("pugOnWidth: " . $pugOnWidth);


        $totalCost = totalCost($quantity ,$FullTextileCost, $Full_Cost_Foam, $Work_Cost, $full_holcon_cost, $full_zipper_cost, $Full_Pug_Cost);
        $finalCost = finalCost($totalCost, $Production_MarkUp, $Wholesale_MarkUp, $Retail_Markup);

        // Проверка наличия ключей в массиве
        if (isset($resultBestFit['rollLength']) && isset($resultBestFit['details'])) {
            $rollSuccess = $resultBestFit['rollLength'];

            // Проверка на валидность данных и расчёт объёма
            if ($width > 0 && $length > 0 && $bold > 0 && $quantity > 0) {
                $volume = ($width * $length * $bold) / 1e9;
                $totalVolume = $volume * $quantity;

                $response = [
                    'success' => true,
                    'roll-length' => $resultBestFit['rollLength'],
                    'textile-cost' => $TextileCost,
                    'full-textile-cost' => $FullTextileCost,
                    'full-cost-foam' => $Full_Cost_Foam,
                    'work-cost' => $Work_Cost,
                    'full-holcon-cost' => $full_holcon_cost,
                    'full-zipper-zost' => $full_zipper_cost,
                    'total-cost' => $totalCost,
                    'final-cost' => $finalCost,
                    'pug-cost' => $Full_Pug_Cost,
                    'double-pug' => $doublePug,
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Неверные данные. Убедитесь, что все значения положительные.'
                ];
            }
        } else {
            throw new Exception('Ошибка в функции bestFit: отсутствуют необходимые ключи в результате.');
        }
        } else {
            $response = [
                'success' => false,
                'message' => 'Неверный метод запроса.'
        ];}
        } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Ошибка сервера: ' . $e->getMessage()
        ];
        }

// Убедитесь, что до этого не было вывода
echo json_encode($response);
exit;


