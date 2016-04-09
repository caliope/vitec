import scrapy

class gooschNResultadosSpider(scrapy.Spider):
    name = 'gooschNResultados'

    def __init__(self, query=None, *args, **kwargs):
        super(gooschNResultadosSpider, self).__init__(*args, **kwargs)
        # Esta consulta por defecto utilza la interfaz en espaniol y trae los resultados en espaniol 
        self.start_urls = ["https://scholar.google.com/scholar?hl=es&lr=lang_es&q=%s" % (query)]

    def parse(self, response):
        resultados = response.xpath('string(.//div[@id="gs_ab_md"])').extract()
        resultados = resultados[0] if len(resultados) > 0 else ""
        resultados = resultados.split(" ")
        for val in resultados:
            if not val.isdigit():
                resultados.remove(val)
        resultados[0] = resultados[0].replace(".", "")
        nresultados = int(resultados[0]) if len(resultados) > 0 else 0
	print nresultados
