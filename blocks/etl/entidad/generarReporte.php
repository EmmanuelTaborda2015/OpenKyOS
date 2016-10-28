<?php
namespace reportes\instalacionesGenerales\entidad;

class GenerarReporteInstalaciones {

    public $miConfigurador;
    public $lenguaje;
    public $miFormulario;
    public $miSql;
    public $conexion;
    public $proyectos;
    public $proyectos_general;

    public function __construct($sql, $proyectos) {

        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->miSql = $sql;
        $this->proyectos = $proyectos;

        $_REQUEST['tiempo'] = time();

        /**
         * 0. Estrucurar Desatelles Proyecto
         **/
        $this->estruturarProyectos();
        
        /**
         * 1. Filtrar Proyectos a Reportear
         **/
        $this->filtrarProyectos();
        
        /**
         * 2. Filtrar Actividades Paquetes de Trabajo
         **/
        $this->detallarCamposPersonalizadosProyecto();

        /**
         * 3. Obtener Paquetes de Trabajo
         **/
        $this->obtenerPaquetesTrabajo();
        
        /**
         * 4. Obtener Actividades Paquetes de Trabajo
         **/
        $this->obtenerActividades();
        
        /**
         * 5. Filtrar Actividades Paquetes de Trabajo
         **/
        $this->filtrarActividades();

        /**
         * 6. Crear Documento Hoja de Calculo(Reporte)
         **/

        $this->crearHojaCalculo();

    }

    public function estruturarProyectos() {

//     	$_REQUEST['info_proyectos'] = 'eyIxMSI6eyJpZCI6MTYsIm5hbWUiOiJDQUJFQ0VSQSCgQzMtTFYtMDQiLCJpZGVudGlmaWVyIjoiY2FiZWNlcmEtbG9yaWNhIiwiZGVzY3JpcHRpb24iOiJDYWJlY2VyYSBMb3JpY2EiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6OTcsInJlc3BvbnNpYmxlX2lkIjotMSwidHlwZV9pZHMiOlsxLDIsM10sImNyZWF0ZWRfb24iOiIyMDE2LTA4LTE4VDAxOjI4OjE1WiIsInVwZGF0ZWRfb24iOiIyMDE2LTA5LTE5VDE5OjIxOjAyWiJ9LCIxMiI6eyJpZCI6MTgsIm5hbWUiOiJMYSBWaWN0b3JpYSIsImlkZW50aWZpZXIiOiJsYS12aWN0b3JpYSIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgTGEgVmljdG9yaWEiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6OTcsInJlc3BvbnNpYmxlX2lkIjotMSwidHlwZV9pZHMiOlsxLDIsM10sImNyZWF0ZWRfb24iOiIyMDE2LTA4LTE4VDAxOjMwOjE1WiIsInVwZGF0ZWRfb24iOiIyMDE2LTEwLTA3VDIxOjI1OjQ3WiJ9LCIxMyI6eyJpZCI6MTcsIm5hbWUiOiJ3TUFOIExvcmljYSIsImlkZW50aWZpZXIiOiJ3bWFuLWxvcmljYSIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgd01BTiBMb3JpY2EiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6OTcsInJlc3BvbnNpYmxlX2lkIjotMSwidHlwZV9pZHMiOlsxLDIsM10sImNyZWF0ZWRfb24iOiIyMDE2LTA4LTE4VDAxOjI5OjA0WiIsInVwZGF0ZWRfb24iOiIyMDE2LTEwLTA3VDIxOjI1OjU3WiJ9LCIxNSI6eyJpZCI6MTAsIm5hbWUiOiJMYSBHbG9yaWEiLCJpZGVudGlmaWVyIjoibGEtZ2xvcmlhIiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSBMYSBHbG9yaWEiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6MTAwLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMToyMDo0OFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMToyNzoyMloifSwiMTYiOnsiaWQiOjEzLCJuYW1lIjoiVmlsbGEgTWVsaXNzYSIsImlkZW50aWZpZXIiOiJ2aWxsYS1tZWxpc3NhIiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSBWaWxsYSBNZWxpc3NhIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjEwMCwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6MjQ6MjdaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6Mjc6MzFaIn0sIjE4Ijp7ImlkIjo3NCwibmFtZSI6IkNBQkVDRVJBIEMxLU1PTi0wMiIsImlkZW50aWZpZXIiOiJjYWJlY2VyYS1jMS1tb24tMDIiLCJkZXNjcmlwdGlvbiI6IkNhYmVjZXJhIEZpbnplbnUiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6MTAxLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOS0xOVQyMDoxMzo0M1oiLCJ1cGRhdGVkX29uIjoiMjAxNi0wOS0yM1QxMzoxMjozOVoifSwiMTkiOnsiaWQiOjEyLCJuYW1lIjoiRmluemVudSIsImlkZW50aWZpZXIiOiJmaW56ZW51IiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSBGaW56ZW51IiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjEwMSwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6MjI6MjVaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6Mjc6NDRaIn0sIjIxIjp7ImlkIjo1LCJuYW1lIjoiQ0FCRUNFUkEgQzUtUFAtMDYiLCJpZGVudGlmaWVyIjoiY2FiZWNlcmEtcGxhbmV0YS1yaWNhIiwiZGVzY3JpcHRpb24iOiJDYWJlY2VyYSBQbGFuZXRhIFJpY2EiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6ODksInJlc3BvbnNpYmxlX2lkIjotMSwidHlwZV9pZHMiOlsxLDIsM10sImNyZWF0ZWRfb24iOiIyMDE2LTA4LTE4VDAxOjE1OjM3WiIsInVwZGF0ZWRfb24iOiIyMDE2LTA5LTE5VDE5OjU3OjU4WiJ9LCIyMiI6eyJpZCI6NywibmFtZSI6IlByaW1lcm8gUGxhbmV0YSIsImlkZW50aWZpZXIiOiJwcmltZXJvLXBsYW5ldGEiLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIFByaW1lcm8gUGxhbmV0YSIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo4OSwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6MTc6MTRaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6Mjc6NTdaIn0sIjIzIjp7ImlkIjo2LCJuYW1lIjoid01BTiBQbGFuZXRhIFJpY2EiLCJpZGVudGlmaWVyIjoid21hbi1wbGFuZXRhLXJpY2EiLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIHdNQU4gUGxhbmV0YSBSaWNhIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjg5LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMToxNjoyM1oiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMToyODoxMVoifSwiMjUiOnsiaWQiOjc3LCJuYW1lIjoiQ0FCRUNFUkEgQzYtRFAtMDciLCJpZGVudGlmaWVyIjoiY2FiZWNlcmEtYzYtZHAtMDciLCJkZXNjcmlwdGlvbiI6IkNhYmVjZXJhIFBvcnRhbCBBZHJpYW5hIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjkxLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOS0xOVQyMDoyMjo0OFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0wOS0yM1QxMzoxMzo0N1oifSwiMjYiOnsiaWQiOjIyLCJuYW1lIjoiUG9ydGFsIGRlIEFkcmlhbmEiLCJpZGVudGlmaWVyIjoicG9ydGFsLWRlLWFkcmlhbmEiLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIFBvcnRhbCBkZSBBZHJpYW5hIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjkxLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTozMzo1MloiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMToyODoyNloifSwiMjciOnsiaWQiOjIxLCJuYW1lIjoid01BTiBQdXLtc2ltYSIsImlkZW50aWZpZXIiOiJ3bWFuLXB1cmlzaW1hIiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSB3TUFOIFB1cu1zaW1hIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjkxLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTozMzowNloiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMToyOTowNloifSwiMzAiOnsiaWQiOjE1LCJuYW1lIjoiQWx0b3MgZGUgQWNhY+1hcyIsImlkZW50aWZpZXIiOiJhbHRvcy1kZS1hY2FjaWFzIiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSBBbHRvcyBkZSBBY2Fj7WFzIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjg3LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMToyNzoxMFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMToyMzo1OVoifSwiMzEiOnsiaWQiOjc1LCJuYW1lIjoiQ0FCRUNFUkEgQzItQUEtMDMiLCJpZGVudGlmaWVyIjoiY2FiZWNlcmEtYzItYWEtMDMiLCJkZXNjcmlwdGlvbiI6IkNhYmVjZXJhIEFsdG9zIGRlIEFjYWNpYXMiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6ODcsInJlc3BvbnNpYmxlX2lkIjotMSwidHlwZV9pZHMiOlsxLDIsM10sImNyZWF0ZWRfb24iOiIyMDE2LTA5LTE5VDIwOjE1OjUxWiIsInVwZGF0ZWRfb24iOiIyMDE2LTA5LTIzVDEzOjEyOjU3WiJ9LCIzMiI6eyJpZCI6MTQsIm5hbWUiOiJ3TUFOIENlcmV06SIsImlkZW50aWZpZXIiOiJ3bWFuLWNlcmV0ZSIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgd01BTiBDZXJldOkiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6ODcsInJlc3BvbnNpYmxlX2lkIjotMSwidHlwZV9pZHMiOlsxLDIsM10sImNyZWF0ZWRfb24iOiIyMDE2LTA4LTE4VDAxOjI2OjIxWiIsInVwZGF0ZWRfb24iOiIyMDE2LTEwLTA3VDIxOjI0OjQ3WiJ9LCIzNCI6eyJpZCI6NzYsIm5hbWUiOiJDQUJFQ0VSQSBDNC1TRi0wNSIsImlkZW50aWZpZXIiOiJjYWJlY2VyYS1jNC1zZi0wNSIsImRlc2NyaXB0aW9uIjoiQ2FiZWNlcmEgU2FuIEZyYW5jaXNjbyIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo5MiwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDktMTlUMjA6MTg6MzJaIiwidXBkYXRlZF9vbiI6IjIwMTYtMDktMjNUMTM6MTM6MTBaIn0sIjM1Ijp7ImlkIjoyMCwibmFtZSI6IlNhbiBGcmFuY2lzY28iLCJpZGVudGlmaWVyIjoic2FuLWZyYW5jaXNjbyIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgU2FuIEZyYW5jaXNjbyIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo5MiwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6MzI6MjVaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6MjU6MDNaIn0sIjM2Ijp7ImlkIjoxOSwibmFtZSI6IndNQU4gTW9taWwiLCJpZGVudGlmaWVyIjoid21hbi1tb21pbCIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgd01BTiBNb21pbCIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo5MiwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6MzE6MjZaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6MjU6MjNaIn0sIjM4Ijp7ImlkIjo4LCJuYW1lIjoiQ0FCRUNFUkEgQzEtTU9OLTAxIiwiaWRlbnRpZmllciI6ImNhYmVjZXJhLWxhLWdsb3JpYS1lbC1yZWN1ZXJkby12aWxsYS1tZWxpc3NhIiwiZGVzY3JpcHRpb24iOiJDYWJlY2VyYSBMYSBHbG9yaWEsIEVsIFJlY3VlcmRvLCBWaWxsYSBNZWxpc3NhIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjg4LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMToxODo1NFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0wOS0xOVQxOTo1NjowNloifSwiMzkiOnsiaWQiOjExLCJuYW1lIjoiRWwgUmVjdWVyZG8iLCJpZGVudGlmaWVyIjoiZWwtcmVjdWVyZG8iLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIEVsIFJlY3VlcmRvIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjg4LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMToyMTozNFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMToyNTozNVoifSwiNDIiOnsiaWQiOjksIm5hbWUiOiJ3TUFOIE1vbnRlcu1hIiwiaWRlbnRpZmllciI6IndtYW4tbW9udGVyaWEiLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIHdNQU4gTW9udGVy7WEiLCJwcm9qZWN0X3R5cGVfaWQiOjIsInBhcmVudF9pZCI6MTAyLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMToxOTo0OFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMToyOToxNVoifSwiNDQiOnsiaWQiOjk0LCJuYW1lIjoiMy5JbnN0YWxhY2nzbiBOb2MgeSBNZXNhIGRlIEF5dWRhIiwiaWRlbnRpZmllciI6ImlucyIsImRlc2NyaXB0aW9uIjoiIiwicHJvamVjdF90eXBlX2lkIjpudWxsLCJwYXJlbnRfaWQiOjMsInJlc3BvbnNpYmxlX2lkIjotMSwidHlwZV9pZHMiOlsxLDIsM10sImNyZWF0ZWRfb24iOiIyMDE2LTA5LTIzVDE1OjQ5OjIwWiIsInVwZGF0ZWRfb24iOiIyMDE2LTA5LTI5VDE3OjIzOjI3WiJ9LCI0OCI6eyJpZCI6MzMsIm5hbWUiOiJDQUJFQ0VSQSBTMy1TSS0xMSIsImlkZW50aWZpZXIiOiJjYWJlY2VyYS1tdW5pY2lwYWwtc2luY2UiLCJkZXNjcmlwdGlvbiI6IkNhYmVjZXJhIE11bmljaXBhbCBTaW5j6SIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo4MiwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6NTI6NTNaIiwidXBkYXRlZF9vbiI6IjIwMTYtMDktMTlUMTk6NDY6MDNaIn0sIjQ5Ijp7ImlkIjozNSwibmFtZSI6IlNhbnRhIElzYWJlbCIsImlkZW50aWZpZXIiOiJzYW50YS1pc2FiZWwiLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIFNhbnRhIElzYWJlbCIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo4MiwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6NTU6NDRaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6Mjk6MjRaIn0sIjUwIjp7ImlkIjozNCwibmFtZSI6IndNQU4gU2luY+kiLCJpZGVudGlmaWVyIjoid21hbi1zaW5jZSIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgd01BTiBTaW5j6SIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo4MiwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6NTQ6NTNaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6Mjk6MzhaIn0sIjUyIjp7ImlkIjoyNSwibmFtZSI6IkFsdG9zIGRlIGxhIFNhYmFuYSIsImlkZW50aWZpZXIiOiJhbHRvcy1kZS1sYS1zYWJhbmEiLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIEFsdG9zIGRlIGxhIFNhYmFuYSIsInByb2plY3RfdHlwZV9pZCI6MiwicGFyZW50X2lkIjo4MywicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6Mzc6MzFaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6MzE6NDhaIn0sIjUzIjp7ImlkIjoyMywibmFtZSI6IkNBQkVDRVJBIFMyLTA5LTAxIiwiaWRlbnRpZmllciI6ImNhYmVjZXJhLWFsdG9zLWRlLWxhLXNhYmFuYS10aWVycmEtZ3JhdGEtdmlsbGEtb3JpZXRhIiwiZGVzY3JpcHRpb24iOiJDYWJlY2VyYSBBbHRvcyBkZSBsYSBTYWJhbmEsIFRpZXJyYSBHcmF0YSwgVmlsbGEgT3JpZXRhIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjgzLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTozNTo0MloiLCJ1cGRhdGVkX29uIjoiMjAxNi0wOS0xOVQxOTo1Mjo0MVoifSwiNTQiOnsiaWQiOjc4LCJuYW1lIjoiQ0FCRUNFUkEgUzItMDktMDIiLCJpZGVudGlmaWVyIjoiY2FiZWNlcmEtczItMDktMDIiLCJkZXNjcmlwdGlvbiI6IkNhYmVjZXJhIFZpbGxhIGthcmVuIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjgzLCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOS0xOVQyMDoyNDozOVoiLCJ1cGRhdGVkX29uIjoiMjAxNi0wOS0yM1QxMzoxNDoxNloifSwiNTciOnsiaWQiOjMwLCJuYW1lIjoiRGlvcyB5IFB1ZWJsbyIsImlkZW50aWZpZXIiOiJkaW9zLXktcHVlYmxvIiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSBEaW9zIHkgUHVlYmxvIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjk1LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTo0MTo1OVoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozMjowM1oifSwiNTgiOnsiaWQiOjI5LCJuYW1lIjoid01BTiBDb3JvemFsIiwiaWRlbnRpZmllciI6IndtYW4tY29yb3phbCIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgd01BTiBDb3JvemFsIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjk1LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTo0MTowNVoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozMjoxNVoifSwiNjAiOnsiaWQiOjM3LCJuYW1lIjoiQ2l1ZGFkZWxhIEplcnVzYWxlbSIsImlkZW50aWZpZXIiOiJjaXVkYWRlbGEtamVydXNhbGVtIiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSBDaXVkYWRlbGEgSmVydXNhbGVtIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjk2LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTo1NzozMloiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozMjozMloifSwiNjEiOnsiaWQiOjM2LCJuYW1lIjoid01BTiBHYWxlcmFzIiwiaWRlbnRpZmllciI6IndtYW4tZ2FsZXJhcyIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgd01BTiBHYWxlcmFzIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjk2LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTo1NjozNloiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozMzoyOFoifSwiNjMiOnsiaWQiOjc5LCJuYW1lIjoiQ0FCRUNFUkEgUzQtTFYtMTIiLCJpZGVudGlmaWVyIjoiY2FiZWNlcmEtczQtbHYtMTIiLCJkZXNjcmlwdGlvbiI6IkNhYmVjZXJhIGxhIFZpY3RvcmlhIiwicHJvamVjdF90eXBlX2lkIjpudWxsLCJwYXJlbnRfaWQiOjg1LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOS0xOVQyMDoyNjozNloiLCJ1cGRhdGVkX29uIjoiMjAxNi0wOS0yM1QxMzoxNDo1MFoifSwiNjQiOnsiaWQiOjMyLCJuYW1lIjoiTGEgVmljdG9yaWEgU2FtcHVlcyIsImlkZW50aWZpZXIiOiJsYS12aWN0b3JpYS1zYW1wdWVzIiwiZGVzY3JpcHRpb24iOiIoUHJveWVjdG8vVXJiYW5pemFjaW9uKSBMYSBWaWN0b3JpYSBTYW1wdWVzIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjg1LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTo0OTo0NFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozNDo0OFoifSwiNjUiOnsiaWQiOjMxLCJuYW1lIjoid01BTiBTYW1wdelzIiwiaWRlbnRpZmllciI6IndtYW4tc2FtcHVlcyIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgd01BTiBTYW1wdelzIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjg1LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTo0MzowN1oiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozNDozNFoifSwiNjciOnsiaWQiOjI2LCJuYW1lIjoiVGllcnJhIEdyYXRhIiwiaWRlbnRpZmllciI6InRpZXJyYS1ncmF0YSIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgVGllcnJhIEdyYXRhIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjk4LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTozODozOVoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozNDoyMVoifSwiNjgiOnsiaWQiOjI3LCJuYW1lIjoiVmlsbGEgT3JpZXRhIiwiaWRlbnRpZmllciI6InZpbGxhLW9yaWV0YSIsImRlc2NyaXB0aW9uIjoiKFByb3llY3RvL1VyYmFuaXphY2lvbikgVmlsbGEgT3JpZXRhIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjk4LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTozOTozNFoiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozNDowOVoifSwiNzAiOnsiaWQiOjI4LCJuYW1lIjoiVmlsbGEgS2FyZW4iLCJpZGVudGlmaWVyIjoidmlsbGEta2FyZW4iLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIFZpbGxhIEthcmVuIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjk5LCJyZXNwb25zaWJsZV9pZCI6LTEsInR5cGVfaWRzIjpbMSwyLDNdLCJjcmVhdGVkX29uIjoiMjAxNi0wOC0xOFQwMTo0MDoxN1oiLCJ1cGRhdGVkX29uIjoiMjAxNi0xMC0wN1QyMTozMzo1NVoifSwiNzIiOnsiaWQiOjI0LCJuYW1lIjoid01BTiBTaW5jZWxlam8iLCJpZGVudGlmaWVyIjoid21hbi1zaW5jZWxlam8iLCJkZXNjcmlwdGlvbiI6IihQcm95ZWN0by9VcmJhbml6YWNpb24pIHdNQU4gU2luY2VsZWpvIiwicHJvamVjdF90eXBlX2lkIjoyLCJwYXJlbnRfaWQiOjEwOCwicmVzcG9uc2libGVfaWQiOi0xLCJ0eXBlX2lkcyI6WzEsMiwzXSwiY3JlYXRlZF9vbiI6IjIwMTYtMDgtMThUMDE6MzY6MzRaIiwidXBkYXRlZF9vbiI6IjIwMTYtMTAtMDdUMjE6MzM6MzlaIn19';

//     	$this->proyectos = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', base64_decode($_REQUEST['info_proyectos'])), true);
//     	var_dump($this->proyectos);
//     	var_dump($_REQUEST);
//     	die;
    	
        foreach ($this->proyectos as $key => $value) {
            $proyectos[] = $value;
        }

        $this->proyectos = $proyectos;

        $this->proyectos_general = $this->proyectos;

    }

    public function obtenerDetalleProyectos() {

        foreach ($this->proyectos as $key => $value) {

            $urlDetalle = $this->crearUrlDetalleProyectos($value['id']);

            $detalle = file_get_contents($urlDetalle);

            $detalle = json_decode($detalle, true);

            $this->proyectos[$key]['custom_fields'] = $detalle['custom_fields'];

        }

    }

    public function crearUrlDetalleProyectos($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function crearHojaCalculo() {
        include_once "crearDocumentoHojaCalculo.php";

    }

    public function detallarCamposPersonalizadosProyecto() {

        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlDetalleProyecto($value['id']);

            $detalleProyecto = file_get_contents($urlPaquetes);

            $detalleProyecto = json_decode($detalleProyecto, true);

            $this->proyectos[$key]['campos_personalizados'] = $detalleProyecto['custom_fields'];

        }
    }

    public function crearUrlDetalleProyecto($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=proyectosDetalle";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function filtrarActividades() {

        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                if (isset($valor['actividades'])) {
                    if ($valor['type_id'] == 2) {

                        foreach ($valor['actividades'] as $llave2 => $actividad) {

                            if ($actividad['_type'] != 'Activity::Comment') {
                                unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);

                            } else {

                                $val = (strpos($actividad['comment']['raw'], 'automáticamente cambiando'));

                                if (is_numeric($val)) {

                                    unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
                                }

                            }

//                             $fecha_actividad = substr($actividad['createdAt'], 0, 10);
//                             $fecha_actividad = strtotime($fecha_actividad);
//                             $fecha_inicio = strtotime($_REQUEST['fecha_inicio']);
//                             $fecha_final = strtotime($_REQUEST['fecha_final']);

//                             if ($fecha_actividad < $fecha_inicio) {
//                                 unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
//                             }

//                             if ($fecha_actividad > $fecha_final) {
//                                 unset($this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][$llave2]);
//                             }

                        }

                    }

                }

            }

        }

    }
    public function obtenerActividades() {
        //var_dump($this->proyectos[2]['paquetesTrabajo']);exit;
        foreach ($this->proyectos as $key => $value) {

            foreach ($value['paquetesTrabajo'] as $llave => $valor) {

                //Avance y  estado instalación NOC

                if ($valor['subject'] === 'Mesa de ayuda') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        //unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);
                    }

                }

                if ($valor['subject'] === 'Centro de gestión') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        //unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);
                    }

                }

                if ($valor['subject'] === 'Otros equipos o sistemas en el NOC') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }
                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        //unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);

                    }

                }

                if ($valor['subject'] === 'Infraestructura nodos') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }
                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        // unset($this->proyectos[$key]['paquetesTrabajo'][$clave]);

                    }

                }

                if ($valor['subject'] === 'Instalación red troncal o interconexión ISP') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }
                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                            $array_ordenado_paquete_trabajo[] = $val;

                        }

                        $variable = $this->proyectos[$key]['paquetesTrabajo'][$clave];

                        if (!empty($variable['child_ids'])) {
                            $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                        }

                    }

                }

                if ($valor['description'] === 'Instalación y puesta en funcionamiento equipos cabecera') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }
                    foreach ($valor['child_ids'] as $llave_a => $contenido) {

                        $urlActividades = $this->crearUrlActividades($contenido);

                        $actividades = file_get_contents($urlActividades);

                        $actividad = json_decode($actividades, true);

                        foreach ($actividad as $avance) {

                            $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                        }

                        $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                        foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                            $array_ordenado_paquete_trabajo[] = $val;

                        }
                        $variable = $array_ordenado_paquete_trabajo[$clave];
                        if (!empty($variable['child_ids'])) {
                            $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                        }

                    }

                }

                if ($valor['subject'] === 'Estado construcción red de distribución') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $this->proyectos[$key]['paquetesTrabajo'][$clave];
                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['subject'] === 'Tendido y puesta en funcionamiento fibra óptica') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];
                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['description'] === 'Infraestructura nodo (Avance y estado instalación nodo EOC)') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];

                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['description'] === 'Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo EOC)') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];

                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

                if ($valor['description'] === 'Infraestructura nodo (Avance y estado instalación nodo inalámbrico)') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            //$variable = $array_ordenado_paquete_trabajo[$clave];

                            $variable = $this->proyectos[$key]['paquetesTrabajo'][$clave];

                            if (!empty($variable['child_ids'])) {

                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }
/*
if ($valor['description'] === 'Instalación y puesta en funcionamiento equipos (Avance y estado instalación nodo inalámbrico)') {

$urlActividades = $this->crearUrlActividades($valor['id']);

$actividades = file_get_contents($urlActividades);

$actividad = json_decode($actividades, true);

foreach ($actividad as $avance) {

$this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
}

if (!empty($valor['child_ids'])) {

foreach ($valor['child_ids'] as $llave_a => $contenido) {

$urlActividades = $this->crearUrlActividades($contenido);

$actividades = file_get_contents($urlActividades);

$actividad = json_decode($actividades, true);

foreach ($actividad as $avance) {

$this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
}

$clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

$array_ordenado_paquete_trabajo[] = $val;

}
$variable = $array_ordenado_paquete_trabajo[$clave];

if (!empty($variable['child_ids'])) {
$this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

}

}

}

}*/

                if ($valor['subject'] === 'Tendido y puesta en funcionamiento red coaxial') {

                    $urlActividades = $this->crearUrlActividades($valor['id']);

                    $actividades = file_get_contents($urlActividades);

                    $actividad = json_decode($actividades, true);

                    foreach ($actividad as $avance) {

                        $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                    }

                    if (!empty($valor['child_ids'])) {

                        foreach ($valor['child_ids'] as $llave_a => $contenido) {

                            $urlActividades = $this->crearUrlActividades($contenido);

                            $actividades = file_get_contents($urlActividades);

                            $actividad = json_decode($actividades, true);

                            foreach ($actividad as $avance) {

                                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
                            }

                            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

                            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                                $array_ordenado_paquete_trabajo[] = $val;

                            }
                            $variable = $array_ordenado_paquete_trabajo[$clave];

                            if (!empty($variable['child_ids'])) {
                                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

                            }

                        }

                    }

                }

            }

        }

    }

    public function obtenerHijosPaquetesTrabajo($contenido = '', $key = '', $llave = '', $variable = '') {

        foreach ($variable['child_ids'] as $llave_a => $contenido) {

            $urlActividades = $this->crearUrlActividades($contenido);

            $actividades = file_get_contents($urlActividades);

            $actividad = json_decode($actividades, true);

            foreach ($actividad as $avance) {

                $this->proyectos[$key]['paquetesTrabajo'][$llave]['actividades'][] = $avance;
            }

            $clave = array_search($contenido, array_column($this->proyectos[$key]['paquetesTrabajo'], 'id'), true);

            foreach ($this->proyectos[$key]['paquetesTrabajo'] as $val) {

                $array_ordenado_paquete_trabajo[] = $val;

            }

            $variable = $array_ordenado_paquete_trabajo[$clave];

            if (!empty($variable['child_ids'])) {
                $this->obtenerHijosPaquetesTrabajo($contenido, $key, $llave, $variable);

            }

        }

    }

    public function crearUrlActividades($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=detalleActividadesPaquetesTrabajo";
        $variable .= "&id_paquete_trabajo=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }

    public function obtenerPaquetesTrabajo() {

        foreach ($this->proyectos as $key => $value) {

            $urlPaquetes = $this->crearUrlPaquetesTrabajo($value['id']);

            $paquetesTrabajo = file_get_contents($urlPaquetes);

            $paquetesTrabajo = json_decode($paquetesTrabajo, true);

            $this->proyectos[$key]['paquetesTrabajo'] = $paquetesTrabajo;

        }

    }

    public function crearUrlPaquetesTrabajo($var = '') {

        // URL base
        $url = $this->miConfigurador->getVariableConfiguracion("host");
        $url .= $this->miConfigurador->getVariableConfiguracion("site");
        $url .= "/index.php?";
        // Variables
        $variable = "pagina=openKyosApi";
        $variable .= "&procesarAjax=true";
        $variable .= "&action=index.php";
        $variable .= "&bloqueNombre=" . "llamarApi";
        $variable .= "&bloqueGrupo=" . "";
        $variable .= "&tiempo=" . $_REQUEST['tiempo'];
        $variable .= "&metodo=paquetesTrabajo";
        $variable .= "&id_proyecto=" . $var;

        // Codificar las variables
        $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
        $cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $enlace);

        // URL definitiva
        $urlApi = $url . $cadena;

        return $urlApi;
    }
    public function filtrarProyectos() {

        foreach ($this->proyectos as $key => $value) {

            $this->proyectos[$key]['name'] = str_replace('?', ' ', utf8_decode($value['name']));

        }

        $cantidadProyectos = count($this->proyectos);

        for ($i = 1; $i < $cantidadProyectos; $i++) {

            if (isset($_REQUEST['item' . $i])) {

                $ident_proyectos[] = $_REQUEST['item' . $i];

            }

        }

        $this->obtenerDetalleProyectos();

        if (isset($ident_proyectos)) {

            foreach ($this->proyectos as $key => $value) {

                foreach ($ident_proyectos as $valor) {

                    if ($value['id'] == $valor) {

                        $proyectos[] = $value;

                        $llave = array_search($value['custom_fields'][3]['value'], array_column($this->proyectos, 'name'), true);

                        if (!is_bool($llave)) {
                            $proyectos[] = $this->proyectos[$llave];
                        }

                        $llave = array_search('ins', array_column($this->proyectos, 'identifier'), true);

                        if (!is_bool($llave)) {
                            $proyectos[] = $this->proyectos[$llave];
                        }

                    }

                }

            }

            $this->proyectos = $proyectos;

        }

    }

}

$miProcesador = new GenerarReporteInstalaciones($this->sql, $this->proyectos);

?>

