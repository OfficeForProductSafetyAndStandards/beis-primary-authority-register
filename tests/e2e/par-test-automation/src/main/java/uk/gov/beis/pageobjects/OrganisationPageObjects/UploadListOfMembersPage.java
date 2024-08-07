package uk.gov.beis.pageobjects.OrganisationPageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class UploadListOfMembersPage extends BasePageObject {
	
	@FindBy(id = "edit-csv-upload")
	private WebElement chooseFileUploadBtn;
	
	@FindBy(id = "edit-upload")
	private WebElement uploadBtn;
	
	private String membersList = "memberslist.csv";
	
	public UploadListOfMembersPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void chooseCSVFile() {
		uploadDocument(chooseFileUploadBtn, membersList);
		
		String[] businessNames = getColumnFromCSV(membersList, 0, ","); // Get the Business Names out of the first column of the csv file. [First Row is Headers]
		DataStore.saveValue(UsableValues.MEMBER_ORGANISATION_NAME, businessNames[1]); // First Business Name Saved. [Row 0 is the Header]
	}
	
	public void clearCSVFile() {
		chooseFileUploadBtn.clear();
	}
	
	public void clickUploadButton() {
		uploadBtn.click();
	}
}
