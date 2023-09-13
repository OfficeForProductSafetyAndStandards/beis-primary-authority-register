package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.utility.DataStore;

public class AdviceNoticeSearchPage extends BasePageObject {

	public AdviceNoticeSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(linkText = "Upload advice")
	WebElement uploadBtn;

	public UploadAdviceNoticePage selectUploadLink() {
		uploadBtn.click();
		return PageFactory.initElements(driver, UploadAdviceNoticePage.class);
	}

	String planstatus = "//td/a[contains(text(),'?')]/parent::td/following-sibling::td[2]";

	public String getAdviceStatus() {
		try {
			return driver
					.findElement(
							By.xpath(planstatus.replace("?", DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE))))
					.getText();
		} catch (Exception e) {
			return ("No results returned");
		}
	}

}