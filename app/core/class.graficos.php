<?php

require_once("class.core.php");
require_once("class.config.php");
require_once("class.help.php");

Class Graficos{

	
	public $core;
	public $config;
	public $bdconn;
	public $help;
	public $tipoGrafico;
	

	public function Graficos($tipoGrafico = 'linha')
	{
		//linha, pizza
		$this->tipoGrafico = $tipoGrafico;
	
		$this->config        = new Config();
		$this->core          = new Core(false);
		$this->help          = new Help();
		$this->bdconn        = new Conexao();
		
		$this->jquery = "";
		
	}
	
	public function setSQLResult($resultSQL,$div='idGrafico')
	{
		$y='';
		foreach ($resultSQL as $result)
		{
			if ($this->tipoGrafico == 'linha')
			{
				//print_r($result);
				foreach ($result as $yty => $x)
				{						
					//echo "[x: ".$x."-"." y: ".$y." yty: ".$yty."] <br/>";
					
					if ($y != '')
					{
						//data | tipoerro | qtd
						$grafico_info[$y][$yty] = $x;
						$y='';
					}
					else
					{
						$y = $x;
					}
					//print_r($grafico_info);
					//echo "<hr/>";
				}	
			}
			else if ($this->tipoGrafico == 'pizza')
			{
				foreach ($result as $x)
				{								
					//print_r($x);
					//echo "<br/>";
					if ($y != '')
					{
						//$grafico_info["Cod ".$y] = $x;
						$grafico_info[$y] = $x;
						$y='';
					}
					else
					{
						$y = $x;
					}
				}
			}
			
		}
		//print_r($grafico_info);
		$this->setDados($grafico_info,$div);
		
	}
	
	
	public function setDados($dados,$div='idGrafico')
	{
		
		$g_data = "";
		$g_js = "";
		$g_cols = array();
		
		foreach ($dados as $gi => $val)
		{
			if ($this->tipoGrafico == 'linha')
			{
				$g_data .= "['".$gi."' ";
				foreach($val as $cols => $val_2)
				{
					$g_data .= ",".$val_2;
					$g_cols[$cols] = 0;
				}
				$g_data .= "],";
			}
			else if ($this->tipoGrafico == 'pizza')
			{
				$g_data .= "['".$gi."', ".$val."],";
			}
		}
		$g_data = substr($g_data, 0, -1);
		
		//print_r($g_data);
		
		$g_js .= "google.charts.load('current', {'packages':['corechart']});";
			
			
		if ($this->tipoGrafico == 'linha')
		{
			$g_js .= "google.charts.setOnLoadCallback(drawChartLine_".$div.");";
		}
		else if ($this->tipoGrafico == 'pizza')
		{
			$g_js .= "google.charts.setOnLoadCallback(drawChart_".$div.");";
		}
		
		$this->g_js = $g_js;
		$this->g_data = $g_data;
		$this->g_cols = $g_cols;
		
		$this->montaJSfunctions($div);
	}
	
	public function montaJSfunctions($div='idGrafico')
	{
		$js = "";
		
		
		if ($this->tipoGrafico == 'linha')
		{
			$js .= "
				function drawChartLine_".$div."() {

					var data = new google.visualization.DataTable();
					data.addColumn('string', 'Data');";
				  
					//if ($this->tipoGrafico == 'linha')
					//{
						foreach($this->g_cols as $c => $cc)
						{
							$js .= "data.addColumn('number', '".$c."'); \n";	
						}
					//}
					
			$js .= " 
					data.addRows([".$this->g_data."]);

					var options = {
						hAxis: {
							title: ''
						},
						vAxis: {
							title: ''
						}
					};

					var chart = new google.visualization.LineChart(document.getElementById('".$div."'));

					chart.draw(data, options);
				
				}";
		}
		else if ($this->tipoGrafico == 'pizza')
		{
		
			$js .= "function drawChart_".$div."() {

				var options = {
				  slices: {
					0: { color: 'red' },
					1: { color: 'green' }
				  },
				  chartArea:{left:0,top:30,width:'100%',height:'250px'}
				};

				var data_grafico = google.visualization.arrayToDataTable([
				  ['Tipo', 'Total'],
				  ".$this->g_data."
				]);

				
				var chart_2 = new google.visualization.PieChart(document.getElementById('".$div."'));
				chart_2.draw(data_grafico, options);

			}";
		
		}

		$this->jquery .= $this->g_js." ".$js;
	
	}
	

}

?>