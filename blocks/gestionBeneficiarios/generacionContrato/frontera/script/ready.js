		/**
		 * Código JavaScript del Bloque
		 */

		    $('#<?php echo $this->campoSeguro("fecha_expedicion");?>').datetimepicker({
        	    format: 'yyyy-mm-dd',
                language: "es",
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            });