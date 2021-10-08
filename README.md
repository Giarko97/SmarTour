# SmarTour

SmarTour è un progetto che permette di creare un tour personalizzato selezionando dei TOI (Topic of Interest). L'applicazione selezionerà i POI (Point of Interest) più affini alle richieste dell'utente e selezionerà un percorso ottimale. 

In questa prima fase di sviluppo ci siamo dedicati all'estrazione dei POI da OpenStreetMap e a determinare la pertinenza relativa ai TOI dei POI.

Il progetto è ancora in fase di sviluppo.

# Areaselection.js
Classe che permette all'utente di visualizzare una mappa e di selezionare una regione dalla quale estrarre i POI. Mappa implementata tramite 'Leaflet'

# App.php 
E' la classe ceh si occupa di interrogare overpass mediante la sua API, di estrarre i POI e di inserirli nel database.

# Pertinence.php
Determina la popolarità di un determinato POI, si basa su tre indici pesati e normalizzati a 1 --> numero di visualizzazioni mensili della pagina wikipedia dedicata al POI, lunghezza in Byte della pagina wikipedia
e numero di lingue in cui è stata tradotta la pagina. Queste informazioni sono state ottenute mediante l'API Wikipedia.

# Altre
Le altre classi si occupano di gestire il popolamento e l'aggiornamento del Database. 
