#!/usr/bin/Rscript  
args <- commandArgs()
library(rmongodb)
library(tm)   
library(jsonlite)
library(SnowballC)
mongo<-mongo.create()
consulta<-list(query=args[6])
docs<-mongo.find(mongo, "vitec.resultado", mongo.bson.from.list(consulta), fields='{ "_id": 0, "titulo": 1, "extracto": 1}')
docs_lst<-mongo.cursor.to.list(docs)
docs_vec<-list()
for ( i in 1:length(docs_lst)){
	docs_vec[i]<-gsub("\n", " ", paste(docs_lst[[i]][2], " ", docs_lst[[i]][1]))
}
docs <- Corpus(VectorSource(docs_vec))        
docs <- tm_map(docs, removePunctuation)
docs <- tm_map(docs, removeNumbers)
docs <- tm_map(docs, tolower)
docs <- tm_map(docs, removeWords, stopwords("english"))
docs <- tm_map(docs, removeWords, stopwords("spanish"))   
docs <- tm_map(docs, stemDocument)
docs <- tm_map(docs, stripWhitespace)
docs <- tm_map(docs, PlainTextDocument)      
dtm <- DocumentTermMatrix(docs)
dtms <- removeSparseTerms(dtm, 0.98)
freq <- colSums(as.matrix(dtms))
toJSON(as.list(freq))