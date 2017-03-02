package ru.firemoon777.metronome;

import android.util.Log;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.net.URL;

/**
 * Created by firemoon on 02.03.17.
 */
public class NetworkHelper {

    private String API = "http://metro.firemoon777.ru/api/";

    public String DownloadFromURL(URL url) {
        try {
            BufferedReader in = new BufferedReader(
                    new InputStreamReader(
                            url.openStream()));

            String inputLine, result = "";

            while ((inputLine = in.readLine()) != null) {
                Log.d("NetworkHelper", "download = " + inputLine);
                result += inputLine;
            }
            return  result;
        } catch (Exception e) {
            e.printStackTrace();
            return null;
        }
    }

    public void RefreshCarriageList() {
        String json;
        try {
            URL url = new URL(API + "carriage.get");
            json = this.DownloadFromURL(url);
        } catch (Exception e) {
            e.printStackTrace();
            return;
        }
        Log.d("NetworkHelper", "Json = " + json);
    }

}
