package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class ArchivePage extends BasePageObject {
	
	@FindBy(id = "edit-archive-reason")
	private WebElement archiveReasonBox;
	
	@FindBy(id = "edit-save")
	private WebElement saveBtn;
	
	public ArchivePage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public AdviceNoticeSearchPage enterReasonForArchiving(String reason) {
		archiveReasonBox.clear();
		archiveReasonBox.sendKeys(reason);
		
		saveBtn.click();
		return PageFactory.initElements(driver, AdviceNoticeSearchPage.class);
	}
}
