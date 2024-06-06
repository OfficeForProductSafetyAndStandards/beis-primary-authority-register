package uk.gov.beis.pageobjects.AdvicePageObjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

import uk.gov.beis.pageobjects.BasePageObject;

public class AdviceArchivePage extends BasePageObject {
	
	@FindBy(id = "edit-archive-reason")
	private WebElement archiveReasonBox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public AdviceArchivePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void enterArchiveReason(String reason) {
		archiveReasonBox.clear();
		archiveReasonBox.sendKeys(reason);
	}
	
	public void selectSaveButton() {
		saveBtn.click();
	}
	
	public AdviceNoticeSearchPage enterReasonForArchiving(String reason) {
		archiveReasonBox.clear();
		archiveReasonBox.sendKeys(reason);
		
		saveBtn.click();
		return PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
	}
}
