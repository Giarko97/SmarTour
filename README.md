# SmarTour

Il progetto è in fase di sviluppo.

# Areaselection.js
Classe che permette all'utente di visualizzare una mappa e di selezionare una regione dalla quale estrarre i POI. Mappa implementata tramite 'Leaflet'

# App.php 
E' la classe ceh si occupa di interrogare overpass mediante la sua API, di estrarre i POI e di inserirli nel database.

# Pertinence.php
Determina la popolarità di un determinato POI, si basa su tre indici pesati e normalizzati a 1 --> numero di visualizzazioni mensili della pagina wikipedia dedicata al POI, lunghezza in Byte della pagina wikipedia
e numero di lingue in cui è stata tradotta la pagina. Queste informazioni sono state ottenute mediante l'API Wikipedia.

# Altre
Le altre classi si occupano di gestire il popolamento e l'aggiornamento del Database. 
