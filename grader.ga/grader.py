import mysql.connector
import time
from os import path
from Garedami.Src import Judge
import requests


dbconnector = mysql.connector.connect(
    host='<<DATABASE IP>>',
    user='<<DATABASE USERNAME>>',
    password='<<DATABASE PASSWORD>>',
    database='<<DATABASE TABLE>>'
)

def getTimeAndMem(idTask):
    response = requests.get(f"http://api.11th.studio/graderga/problem?id={idTask}")
    if response.status_code != 200:
        return -69,-420
    data = response.json()[0]
    return data["time"],data["memory"]

def getWaitSubmission():
    response = requests.get("http://api.11th.studio/graderga/submission?wait&key=<<PRIVATE KEY>>")
    if response.status_code != 200:
        return []
    return response.json()




if __name__ == '__main__':
    
    mycursor = dbconnector.cursor(buffered=True)
    webLocation = "/" + path.join("var","www","grader.ga")

    print("Grader.py started")

    while(1):
        queue = getWaitSubmission()
        if (len(queue)):
            print("Founded Waiting Queue : ",len(queue))
            print(queue)
        for myresult in queue:
            print(myresult['id'])
            #Get data from query
            subID = myresult['id'] #id is the 1st.
            userID = myresult['user'] #user is the 2nd.
            probID = str(myresult['problem']) #problem is the 3rd.
            lang = myresult['lang'] #lang is the 4th.
            userCodeLocation = myresult['script'].replace("..",webLocation) #script location is the 5th.
            #userCodeLocation in format "../file/judge/upload/<User ID>/<Codename>-<EPOCH>.<lang>", real location need change "../" to webLocation
            #Full path: /var/www/grader.ga/file/judge/upload/<User ID>/<Codename>-<EPOCH>.<lang>

            print(f"----------<OwO>----------\nFound Waiting Judge on queue: submission={subID}, problem={probID}, user={userID}")

            probTestcaseLocation = path.join(webLocation,"file","judge","prob",probID)
            #print(probTestcaseLocation)
            #All testcases will be here

            srcCode = ""

            with open(userCodeLocation,"r") as f:
                srcCode = f.read()

            probTime,probMem = getTimeAndMem(probID)

            if probTime < 0:
                judgeResult = ("WebError",0,100,0,0,"Web API Down")
            else:
                judgeResult = Judge.judge(probID,lang,probTestcaseLocation,srcCode)
            #Result from judge
            result = judgeResult[0]
            score = int(judgeResult[1])
            maxScore = int(judgeResult[2])
            runningTime = int(judgeResult[3]) #ms
            memory = int(judgeResult[4]) #MB
            comment = judgeResult[5]

            #Update to SQL
            query = ("UPDATE `submission` SET `result` = %s,`score` = %s,`maxScore` = %s,`runningTime` = %s,`memory` = %s,`comment` = %s WHERE `id` = %s")
            data = (result, score, maxScore, runningTime, memory, comment, subID) #Don't forget to add subID
            mycursor.execute(query, data)
            print(f"Finished Judge submission={subID}, problem={probID}, user={userID} --> {result}")

            #Make sure that query is done.
            dbconnector.commit()
            time.sleep(1)
        dbconnector.commit()
        #Time sleep interval for 1 second.
        time.sleep(1)
