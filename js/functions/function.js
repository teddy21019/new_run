function timeInterval(time1, time2) {
    //time = [12, 34, 56]

    //time 1 to second
    let time1Second = 60 * 60 * time1[0] +
    60 * time1[1] +
    time1[2];
    let time2Second = 60 * 60 * time2[0] +
    60 * time2[1] +
    time2[2];
    
    timeDiff = (time1Second - time2Second)
    timeDiff *= (timeDiff < 0) ? -1 : 1;
    
    //back to array
    
    let sec = timeDiff%60 ;
    let min = Math.floor(timeDiff/60)%60;
    let hour = Math.floor(timeDiff/(60*60))%60;
    return [hour, min, sec];
}

function timeStrToArray(str) {
    //str = "12:34:56"
    //to
    // [12, 34, 56]
    return str.split(':').map(val=>parseInt(val));
}

function timeArrayToStr(arr){
    arr =  arr.map(val=>val.toString().padStart(2, "0"));
    return `${arr[0]}:${arr[1]}:${arr[2]}`;
}