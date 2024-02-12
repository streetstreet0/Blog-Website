function dropdownFormComfirm(promptText, form, dropdown) {
    event.preventDefault();
    let articleName = (dropdown.options[dropdown.selectedIndex]).text;
    if (confirm(promptText + " " + articleName + "?")) {
        form.submit();
    }
}

function blogFormAlert(promptText, titleHolder, idHolder) {
    let blogTitle = titleHolder.value;
    if (blogTitle == "") {
        blogTitle = "article " + idHolder.value;
    }
    alert(promptText + " " + blogTitle);
}

function changeSortBy(sortBySelection) {
    window.location.replace("blogMenu.php?mode=select&sort=" + sortBySelection.value);
}

function changeFilter(filterCheckboxes, index) {
    let filter = "";
    for (let i=0; i<filterCheckboxes.length; i++) {
        filter += "1";
    }
    console.log(filter);
    for (let i=0; i<filterCheckboxes.length; i++) {
        if (!filterCheckboxes[i].checked) {
            filter = replaceCharAt(filter, 0, i);
        }
    }
    window.location.replace("https://localhost/blogMenu.php?mode=select&filter=" + filter);
}

function replaceCharAt(string, replaceChar, index) {
    let firstSubString = string.slice(0, index);
    let secondSubString = string.slice(index+1, string.length);
    return firstSubString + replaceChar + secondSubString; 
}

function tagAlreadyInTable(editTagsTable, tagName) {
    let editTagsTableRows = editTagsTable.getElementsByTagName("tr");
    // empty strings an ones that contain commas aren't allowed
    if (tagName.length == 0) {
        return "Tag must not be an empty string.";
    }
    if (tagName.includes(",")) {
        return "Tags cannot include commas.";
    }
    // first two rows are not tags
    for (let i= 2; i < editTagsTableRows.length; i++) {
        // first item in the row is the name of the tag
        if (editTagsTableRows[i].getElementsByTagName("td")[0].innerHTML == tagName) {
            return "Tag already exists.";
        }
    }
    return "";
}

function addNewTag(editTagsTable, addTagTextbox) {
    let tagName = addTagTextbox.value.trim();
    let errorMessage = tagAlreadyInTable(editTagsTable, tagName) 
    if (errorMessage.length != 0) {
        alert("Error: " + errorMessage);
    }
    else {
        addNewTagToTable(editTagsTable, tagName);
    }
    addTagTextbox.value = "";
}

function addNewTagToTable(editTagsTable, tagName) {
    let newTagRow = editTagsTable.insertRow();
    let tagTitleCell = newTagRow.insertCell();
    let tagCheckboxCell = newTagRow.insertCell(1);

    tagTitleCell.innerHTML = tagName;
    tagCheckboxCell.innerHTML = "<input type=\"checkbox\" class=\"tagCheckbox\" name=\"tags[]\" id=\"" + tagName + "\" value=\"" + tagName + "\">"; 
}

// save the new tags
function keepNewTags(editTagsTable, editTagArticleSelection) {
    let rows = editTagsTable.getElementsByTagName("tr");
    // console.log(rows[0].innerHTML);
    let tagRows = new Array();
    for (let i=0; i<rows.length; i++) {
        tagRows.push(rows[i].getElementsByTagName("td")[0].innerHTML);
        // console.log(rows[i].getElementsByTagName("td")[0].innerHTML);
    }
    let article = editTagArticleSelection.value;
    localStorage.setItem("tagRows", JSON.stringify(tagRows));
    window.location.replace("https://localhost/blogMenu.php?mode=tags&article=" + article);
    // need to wait for the onload to continue the code
}

window.onload = function() {
    let tagRows = JSON.parse(localStorage.getItem("tagRows"));
    let editTagsTable = document.getElementById("editTagsTable");
    if (editTagsTable != null && tagRows != null) {
        let rows = document.getElementById("editTagsTable").getElementsByTagName("tr");
        // console.log(rows.length);
        // console.log(tagRows.length);
        for (let i=rows.length; i<tagRows.length; i++) {
            addNewTagToTable(editTagsTable, tagRows[i]);
            console.log(tagRows[i]);
            // let returningTagRow = editTagsTable.insertRow();
            // returningTagRow.innerHTML = tagRows[i];
        }
    }
    localStorage.clear();
}

// deleteBlog confirmation
let deleteBlog = document.getElementById("deleteBlog");
let deleteBlogDropdown = document.getElementById("deleteBlogDropdown");
if (deleteBlog != null) {
    deleteBlog.addEventListener("submit", function() {dropdownFormComfirm("Are you sure you want to delete", deleteBlog, deleteBlogDropdown)});
}

// addBlog alert
let addBlog = document.getElementById("addBlog");
let addBlogTitleHolder = document.getElementById("addBlogTitle");
let addBlogIdHolder = document.getElementById("addBlogId");
if (addBlog != null) {
    addBlog.addEventListener("submit", function() {blogFormAlert("Successfully created", addBlogTitleHolder, addBlogIdHolder)});
}

// editBlog alert
let editBlog = document.getElementById("editBlog");
let editBlogTitleHolder = document.getElementById("editBlogTitle");
let editBlogIdHolder = document.getElementById("editBlogId");
if (editBlog != null) {
    editBlog.addEventListener("submit", function() {blogFormAlert("Successfully updated", editBlogTitleHolder, editBlogIdHolder)});
}

// sortBy functionality
let sortBySelection = document.getElementById("sortByDropdown");
if (sortBySelection != null) {
    sortBySelection.addEventListener("change", function() {changeSortBy(sortBySelection)});
}

// change filter tags functionality
let filterCheckboxes = document.getElementsByClassName("filterCheckbox");
// filterCheckboxes.sort(function() {return a.value - b.value});
for (let i=0; i<filterCheckboxes.length; i++) {
    filterCheckboxes[i].addEventListener("change", function() {changeFilter(filterCheckboxes, i)});
}

// add new tag
let addTagButton = document.getElementById("newTagButton");
let addTagTextbox = document.getElementById("newTagName");
let editTagsTable = document.getElementById("editTagsTable");
if (addTagButton != null) {
    addTagButton.addEventListener("click", function() {addNewTag(editTagsTable, addTagTextbox)});
}

// edit tags
let editTagForm = document.getElementById("editTags");
if (editTagForm != null) {
    editTagForm.addEventListener("submit", function() {alert("Tags updated");});
}

// select article to edit tags for
let editTagArticleSelection = document.getElementById("editTagsBlogSelection");
if (editTagArticleSelection != null) {
    editTagArticleSelection.addEventListener("change", function() {keepNewTags(editTagForm, editTagArticleSelection)});
}

console.log("js loaded");