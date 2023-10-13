package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

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
	
	String planstatus = "//td/a[contains(text(),'?')]/parent::td/following-sibling::td[2]";
	String noResultsReturned = "//p[contains(text(), 'Sorry, there are no results for your search.')]";
	
	public AdviceNoticeSearchPage() throws ClassNotFoundException, IOException {
		super();
	}

	public AdviceNoticeSearchPage searchForAdvice(String title) {
		adviceSearchBar.clear();
		adviceSearchBar.sendKeys(title);
		
		searchBtn.click();
		return PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
	}
	
	public UploadAdviceNoticePage selectUploadLink() {
		uploadBtn.click();
		return PageFactory.initElements(driver, UploadAdviceNoticePage.class);
	}
	
	public AdviceNoticeDetailsPage selectEditAdviceButton() {
		WebElement editLink = driver.findElement(By.partialLinkText("Edit"));
		editLink.click();
		
		return PageFactory.initElements(driver, AdviceNoticeDetailsPage.class);
	}
	
	public AdviceArchivePage selectArchiveAdviceButton() {
		WebElement archiveLink = driver.findElement(By.partialLinkText("Archive"));
		archiveLink.click();
		
		return PageFactory.initElements(driver, AdviceArchivePage.class);
	}
	
	public AdviceRemovalPage selectRemoveAdviceButton() {
		WebElement removeLink = driver.findElement(By.partialLinkText("Remove"));
		removeLink.click();
		
		return PageFactory.initElements(driver, AdviceRemovalPage.class);
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