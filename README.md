##Videostation##

ACHTUNG: Dieses Packet ist (noch) nicht kompatibel zur Video Station von Synology. Heisst, falls Video Station von Synology installiert ist, kann dieses Packet nicht installiert werden, ohne dass dabei automatisch die Video Station von Synology entfernt wird. Umgekehrt genauso.

Das ist die deutsche Version des Videostation-Apps für die Synology Diskstation(bzw. DSM).
Die originale Version ist hier zu finden: [Videostation](https://github.com/teebo/VideoStation). Ist jedoch auf Französisch.

Ich habe die Language-Files übersetzt und das Script so erweitert, dass es auch deutsche Texte(Inhaltsangaben, Titel)
empfängt. Des weiteren hab ich das Script so modifiziert, dass direkt im Webbrowser gestreamt werden kann. Hierzu ist noch 
das Plugin "Windows Media Player Plugin" für Firefox(ob es für andere Browser auch verfügbar ist, weiss ich nicht). Der Download
dieses Plugins ist hier zu finden : (http://www.interoperabilitybridges.com/windows-media-player-firefox-plugin-download).

#Installation#

- Download des SPK-Packages: [Download](https://github.com/teebo/VideoStation/blob/master/PACKAGE/VideoStation-latest.spk?raw=true)
- Im Paketzentrum auf "Installieren/Aktualisieren" gehen um die Datei(Videostation-latest.spk) hochzuladen.
- Anleitung folgen
- Nach Abschluss der Installation auf http://diskstation/video gehen um die Installation des Scripts zu beenden.

Achtung: Die Webstation muss aktiviert sein, damit dieses Packet funktioniert.

Alles weitere dürfte sich von selbst erklären.

#Changelog#

- 12.07.2012
	- Repo erstellt und Dateien veröffentlicht.
	- README geschrieben