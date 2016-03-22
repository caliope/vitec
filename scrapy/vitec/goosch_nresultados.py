import scrapy

class gooschNResultadosSpider(scrapy.Spider):
    name = 'gooschNResultados'
    start_urls = ["https://scholar.google.com/scholar?hl=es&q=cultivo+de+lulo"]

    def parse(self, response):
        resultados = response.xpath('string(.//div[@id="gs_ab_md"])').extract()
        resultados = resultados[0] if len(resultados) > 0 else ""
        resultados = resultados.split(" ")
        for val in resultados:
            if not val.isdigit():
                resultados.remove(val)
        resultados[0] = resultados[0].replace(".", "")
        nresultados = int(resultados[0]) if len(resultados) > 0 else 0
        yield {
            'valor': nresultados,
        }
