import scrapy

from scrapy.http import Request

from vitec.items import ScholarItem

class ScholarSpider(scrapy.Spider):
    name = "scholar"

    def __init__(self, query=None, *args, **kwargs):
        super(ScholarSpider, self).__init__(*args, **kwargs)
        self.start_urls = ["https://scholar.google.com/scholar?q=%s&btnG=&hl=es&lr=lang_es" % query]

    def parse(self, response):
        for sel in response.xpath('//div[@class="gs_ri"]'):
            item = ScholarItem()
            item['tipo'] = sel.xpath('.//span[@class="gs_ct1"]/text()').extract()
            if len(item['tipo']) > 0:
                item['tipo'] = item['tipo'][0].lstrip('[').rstrip(']')
            else:
                item['tipo'] = ''
            item['titulo'] = sel.xpath('string(.//h3)').extract()
            item['titulo'] = item['titulo'][0]
            inicio = item['titulo'].find('] ') + 1
            final = len(item['titulo'])
            item['titulo'] = item['titulo'][inicio:final]
            item['url'] = sel.xpath('.//h3/a/@href').extract()
            if len(item['url']) > 0:
                item['url'] = item['url'][0]
            else:
                item['url'] = ''
            cadena = sel.xpath('string(.//div[@class="gs_a"])').extract()
            cadenas = cadena[0].split('-')
            item['autores'] = ''
            item['descripcion'] = ''
            item['anio'] = ''
            if cadenas > 0:
                item['autores'] = cadenas[0].split(',')
                if len(cadenas) > 1:
                    cadena = cadenas[1].split(',')
                    if len(cadena) > 1:
                        item['descripcion'] = cadena[0].strip()
                        anio = cadena[1].split()
                    else:
                        anio = cadena[0].split()
                    item['anio'] = anio[0]
                    if len(cadenas) > 2:
                        sitio = cadenas[2]
            item['texto'] = sel.xpath('string(.//div[@class="gs_rs"])').extract()
            item['texto'] = item['texto'][0]
            cadena = sel.xpath('.//div[@class="gs_fl"]/a/text()').extract()
            citado = cadena[0].split(' ')
            if len(citado) > 1:
                if citado[0] == 'Citado':
                    item['citado'] = citado[2]
            if len(cadena) > 1:
                versiones = cadena[2].split()
                item['versiones'] = '0'
                if versiones[0] == 'Las':
                    item['versiones'] = versiones[1]
            yield item

        siguiente_url = response.xpath('//a[contains(., "Siguiente")]/@href').extract()
        print siguiente_url
        if len(siguiente_url) > 0:
            siguiente_url = siguiente_url[0]
            inicio = siguiente_url.find("start")
            final = siguiente_url.find("&", inicio)
            numero = siguiente_url[inicio:final]
            numero = numero.split("=")
            numero = int(numero[1])
            if numero < 300:
                siguiente_url = "https://scholar.google.com" + siguiente_url
                yield Request(siguiente_url, self.parse)
        else:
            print "Robot detectado ....."
