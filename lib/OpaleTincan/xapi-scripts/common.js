Opale = {};

Opale.CourseActivity = {
    id: "http://formagri.com/Aparent",
    definition: {
        type: "http://adlnet.gov/expapi/activities/course",
        name: {
            "fr-FR": "Aparent - Tin Can Course"
        },
        description: {
            "fr-FR": "Adescription"
        }
    }
};

Opale.getContext = function(parentActivityId) {
    var ctx = {
        contextActivities: {
            grouping: {
                id: Opale.CourseActivity.id
            }
        }
    };
    if (parentActivityId !== undefined && parentActivityId !== null) {
        ctx.contextActivities.parent = {
            id: parentActivityId
        };
    }
    return ctx;
};
