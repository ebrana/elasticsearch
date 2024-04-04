FROM docker.elastic.co/elasticsearch/elasticsearch:8.12.0

RUN bin/elasticsearch-plugin install analysis-icu

COPY --chown=elasticsearch:root ./cs_CZ.aff /usr/share/elasticsearch/config/hunspell/cs_CZ/cs_CZ.aff
COPY --chown=elasticsearch:root ./cs_CZ.dic /usr/share/elasticsearch/config/hunspell/cs_CZ/cs_CZ.dic
