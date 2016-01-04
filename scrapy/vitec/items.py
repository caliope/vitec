import scrapy

class ScholarItem(scrapy.Item):
    tipo = scrapy.Field()
    titulo = scrapy.Field()
    url = scrapy.Field()
    cid = scrapy.Field()
    autores = scrapy.Field()
    fuente = scrapy.Field()
    anio = scrapy.Field()
    extracto = scrapy.Field()
    citado = scrapy.Field()
    versiones = scrapy.Field()
    sitio = scrapy.Field()
    query = scrapy.Field()

class MedlineItem(scrapy.Item):
	titulo = scrapy.Field()
	url = scrapy.Field()
	fuente = scrapy.Field()
	extracto = scrapy.Field()