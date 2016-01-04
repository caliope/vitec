# -*- coding: utf-8 -*-
import scrapy
import re

from scrapy.http import Request

from vitec.items import MedlineItem


class MedlineSpider(scrapy.Spider):
    name = "medline"
    fin = 0

    def __init__(self, query=None, inicio=0, final=10, *args, **kwargs):
        super(MedlineSpider, self).__init__(*args, **kwargs)
        self.start_urls = ["https://vsearch.nlm.nih.gov/vivisimo/cgi-bin/query-meta?v:project=medlineplus-spanish&v:sources=medlineplus-spanish-bundle&query=%s" % query]
        print self.start_urls
        self.fin = int(final)

    def parse(self, response):
        nresultados = response.xpath('string(.//span[@class="intronum"])').extract()
        nresultados = int(nresultados[0]) if len(nresultados) > 0 else 0
        self.fin = self.fin if self.fin < nresultados else nresultados
        for sel in response.xpath('.//ol[@class="results"]/li'):
        	titulo = sel.xpath('string(.//a[@class="title"])').extract()
        	titulo = titulo[0].encode('utf8') if len(titulo) > 0 else ''
        	fuente = sel.xpath('.//div[@class="document-header"]/text()').extract()
        	fuente = fuente[0].encode('utf8') if len(fuente) > 0 else ''
        	fuente = fuente.lstrip(" (")
        	fuente = fuente.rstrip(") ")	
        	extracto = sel.xpath('string(.//div[@class="document-snippet"])').extract()
        	extracto = extracto[0].encode('utf8') if len(extracto) > 0 else ''
        	url = sel.xpath('string(.//span[@class="url"])').extract()
        	url = url[0] if len(url) > 0 else ''
        siguiente_url = response.xpath('//a[contains(., "Siguiente")]/@href').extract()
        if len(siguiente_url) > 0:
        	siguiente_url = siguiente_url[0]
        	partes = siguiente_url.split("&")
        	for parte in partes:
        		if parte.count("state") > 0:
        			inicio = parte.index("%")+3
        			fin = parte.index("%", inicio)
        			parte = parte[inicio:fin].split('-')        		
        			resultados = int(parte[1]) 
        	if resultados < self.fin:
        		siguiente_url = "https://vsearch.nlm.nih.gov" + siguiente_url
        		print siguiente_url 
            	yield Request(siguiente_url, self.parse)

