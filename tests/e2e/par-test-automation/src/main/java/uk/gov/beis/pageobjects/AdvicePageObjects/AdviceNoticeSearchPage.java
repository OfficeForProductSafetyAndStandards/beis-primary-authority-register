package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class AdviceNoticeSearchPage extends BasePageObject {

	@FindBy(id = "edit-keywords")
	private WebElement adviceSearchBar;

	@FindBy(id = "edit-submit-advice-lists")
	private WebElement searchBtn;

	@FindBy(linkText = "Upload advice")
	private WebElement uploadBtn;

	private String planstatus = "//td/a[contains(text(),'?')]/parent::td/following-sibling::td[2]";
	private String noResultsReturned = "//p[contains(text(), 'Sorry, there are no results for your search.')]";

	public AdviceNoticeSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	public void searchForAdvice(String title) {
		adviceSearchBar.clear();
		adviceSearchBar.sendKeys(title);

		searchBtn.click();
	}

	public void selectUploadLink() {
		uploadBtn.click();
	}

	public void selectEditAdviceButton() {
		WebElement editLink = driver.findElement(By.partialLinkText("Edit"));
		editLink.click();
        waitForPageLoad();
	}

	public void selectArchiveAdviceButton() {
		WebElement archiveLink = driver.findElement(By.partialLinkText("Archive"));
		archiveLink.click();
        waitForPageLoad();
	}

	public void selectRemoveAdviceButton() {
		WebElement removeLink = driver.findElement(By.partialLinkText("Remove"));
		removeLink.click();
        waitForPageLoad();
	}

	public String getAdviceStatus() {

		try {
			return driver.findElement(By.xpath(planstatus.replace("?", DataStore.getSavedValue(UsableValues.ADVICENOTICE_TITLE)))).getText();
		} catch (Exception e) {
			return ("No results returned");
		}
	}

	public Boolean checkNoResultsReturned() {
		return driver.findElement(By.xpath(noResultsReturned)).isDisplayed();
	}
}
