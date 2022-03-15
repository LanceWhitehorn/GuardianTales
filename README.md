# Guardian Tales

## Background
Guardian Tales is a mobile gacha RPG game that I've played since October 2020. For people unfamiliar with the game, there is a mode called 'Guild Raid' where guilds of 30 members hit progressively harder bosses to achieve the highest score they can over a fornight. The game does not provide historical records so I put together spreadsheets on Google Sheets to record our performances and created an interactive 'dashboard' to summarise performance by guild (https://bit.ly/gt-raid-dashboard) and by guild member (https://bit.ly/gt-member-dashboard). However, this was not a very user-friendly way to access and display the data, so I built a simple webpage that was more intuitive and user-friendly!

## Link
I currently host this at http://cpaa.servicomp.net.au/gt

## Process
Unfortunately, there is no straight forwrd method to gather game data. So, I used this work-around:
1) Using an in-built screen recorder, I scroll through the guild rankings and guild member rankings.
2) I wrote a Python script that:
   * Takes screenshots of the recording every 0.1s 
   * Extracts the information I need using [Tesseract OCR](https://github.com/UB-Mannheim/tesseract/wiki) 
   * Saves the data into a pandas dataframe and exports it as a CSV file
3) Import the data into my database.
