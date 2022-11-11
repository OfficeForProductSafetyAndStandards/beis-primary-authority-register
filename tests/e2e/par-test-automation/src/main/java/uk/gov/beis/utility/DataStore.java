package uk.gov.beis.utility;

import java.util.HashMap;
import java.util.List;

import uk.gov.beis.enums.UsableValues;

public class DataStore {

	private static DataStore instance;
	public static HashMap<String,String> savedValues;
	public static HashMap<String, List<String>> savedGroups;
	
	private DataStore() {
		savedValues = new HashMap<String, String>();
		savedGroups = new HashMap<String, List<String>>();
	}
	
	public static DataStore instance() {
		return instance == null ? resetDataStore():instance;
	}
	
	public static DataStore resetDataStore() {
		return instance = new DataStore();
	}
	
	public static String getSavedValue(UsableValues name) {
		return getSavedValue(name.toString());
	}
	
	public static String getSavedValue(String name) {
		instance();
		return DataStore.savedValues.get(name);
	}
	
	public static String saveValue(UsableValues key, String value) {
		return saveValue(key.toString(),value);
	}
	
	public static String saveValue(UsableValues key, int value) {
		return saveValue(key.toString(), String.valueOf(value));
	}
	
	public static String saveValue(String key, String value) {
		instance();
		DataStore.savedValues.put(key, value);
		return value;
	}
	
}
