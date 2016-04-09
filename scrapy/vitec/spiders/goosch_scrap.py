import scrapy
import re

from scrapy.http import Request

from vitec.items import ScholarItem

class GooschSpider(scrapy.Spider):
    name = "goosch"
    fin = 0 
    query = ""
    bfin = True

    def __init__(self, query=None, inicio=0, final=10, id_query="", *args, **kwargs):
        super(GooschSpider, self).__init__(*args, **kwargs)
        # Esta consulta por defecto utilza la interfaz en espaniol y trae los resultados en espaniol 
        self.start_urls = ["https://scholar.google.com/scholar?start=%s&lr=lang_es&q=%s&hl=es" % (inicio, query)]
        self.fin = int(final)
        self.query = id_query

    def parse(self, response):
    	if self.bfin:
    		resultados = response.xpath('string(.//div[@id="gs_ab_md"])').extract()
    		resultados = resultados[0] if len(resultados) > 0 else ""
    		resultados = resultados.split(" ")
    		for val in resultados:
    			if not val.isdigit():
    				resultados.remove(val)
    		resultados[0] = resultados[0].replace(".", "")
    		nresultados = int(resultados[0]) if len(resultados) > 0 else 0
        	self.fin = self.fin if self.fin < nresultados else nresultados
        	self.bfin = False
        for sel in response.xpath('//div[@class="gs_ri"]'):
            cadena = sel.xpath('string(.//h3[@class="gs_rt"])').extract()
            cadena = cadena[0]
            cadena = cadena.split("] ")
            if len(cadena) > 1:
                titulo = cadena[1].encode('utf8')
                inicio = cadena[0].find('[') + 1
                final = cadena[0].find(']')
                tipo = cadena[0][inicio:final]
            else:
            	titulo = cadena[0].encode('utf8')
            	tipo = ""
            url = sel.xpath('.//h3/a/@href').extract()
            if len(url) > 0:
                url = url[0]
            else:
                url = ''
            cadena = sel.xpath('string(.//div[@class="gs_a"])').extract()
            cadena = cadena[0].split(' - ')
            autores = cadena[0].split(', ')
            sitio = ""
            anio = ''
            fuente = ''
            if len(cadena) > 1:
            	cadena_a = cadena[1].split(', ')
            	if len(cadena_a) == 1:
            		if cadena_a[0].isdigit():
            			anio = cadena_a[0]
            		else:
            			fuente = cadena_a[0]
            	elif len(cadena_a) == 2:
            		fuente = cadena_a[0]
            		anio = cadena_a[1]
            if len(cadena) > 2:
            	sitio = cadena[2].encode('utf8')
            extracto = sel.xpath('string(.//div[@class="gs_rs"])').extract()
            extracto = extracto[0].encode('utf8')
            cadena = sel.xpath('.//div[@class="gs_fl"]/a[contains(.//text(), "Citado")]').extract()
            cid = ''
            citado = ''
            if len(cadena) > 0:
            	cadena = cadena[0]
            	inicio = cadena.find('cites=') + 6
            	final = cadena.find('&', inicio)
            	cid = cadena[inicio:final]
            	inicio = cadena.find('Citado por') + 10
            	final = cadena.find('<', inicio)
            	citado = cadena[inicio:final]
            cadena = sel.xpath('.//div[@class="gs_fl"]/a[contains(.//text(), "versiones")]').extract()
            versiones = ''
            if len(cadena) > 0:
            	cadena = cadena[0]
            	if cid == '':
            		inicio = cadena.find('cluster=') + 8
            		final = cadena.find('&', inicio)
            		cid = cadena[inicio:final]
            	inicio = cadena.find('>Las') + 4
            	final = cadena.find('versiones', inicio)
            	versiones = cadena[inicio:final]

            item = ScholarItem()
            item['titulo'] = titulo
            item['tipo'] = tipo
            item['autores'] = autores
            item['url'] = url
            item['sitio'] = sitio
            item['anio'] = anio
            item['fuente'] = fuente
            item['extracto'] = extracto
            item['cid'] = cid
            item['citado'] = citado
            item['versiones'] = versiones
            item['query'] = self.query
            yield item

        siguiente_url = response.xpath('//a[contains(., "Siguiente")]/@href').extract()
        if len(siguiente_url) > 0:
            siguiente_url = siguiente_url[0]
            inicio = siguiente_url.find("start")
            final = siguiente_url.find("&", inicio)
            numero = siguiente_url[inicio:final]
            numero = numero.split("=")
            numero = int(numero[1])
            if numero < self.fin:
                siguiente_url = "https://scholar.google.com" + siguiente_url
                yield Request(siguiente_url, self.parse)
        #else:
            #print "Robot detectado ....."